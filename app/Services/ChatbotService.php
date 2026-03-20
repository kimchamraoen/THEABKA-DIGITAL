<?php

namespace App\Services;

use App\Models\ChatbotDocument;
use App\Models\ChatbotSetting;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ChatbotService
{
    /**
     * Resolve which AI provider to use for a given user.
     */
    public function resolveProvider(User $user): string
    {
        $userSettings = $user->chatbotSettings;

        if ($userSettings && ChatbotSetting::get('CHATBOT_ALLOW_USER_API_KEY') === '1') {
            if ($userSettings->provider && $this->userHasKeyForProvider($userSettings, $userSettings->provider)) {
                return $userSettings->provider;
            }
        }

        $defaultProvider = ChatbotSetting::get('CHATBOT_DEFAULT_PROVIDER', 'auto');

        if ($defaultProvider === 'auto') {
            $openaiKey = ChatbotSetting::get('OPENAI_API_KEY');
            if ($openaiKey) return 'openai';
            $geminiKey = ChatbotSetting::get('GEMINI_API_KEY');
            if ($geminiKey) return 'gemini';
            return 'openai';
        }

        return $defaultProvider;
    }

    /**
     * Resolve the model to use for a given provider.
     */
    public function resolveModel(string $provider): string
    {
        $settingKey = $provider === 'openai' ? 'OPENAI_MODEL' : 'GEMINI_MODEL';
        $model = ChatbotSetting::get($settingKey);

        if ($model) return $model;

        return match ($provider) {
            'openai' => 'gpt-4o-mini',
            'gemini' => 'gemini-2.5-flash',
            default => 'gpt-4o-mini',
        };
    }

    /**
     * Resolve the API key for the given provider.
     */
    public function resolveApiKey(User $user, string $provider): ?string
    {
        if (ChatbotSetting::get('CHATBOT_ALLOW_USER_API_KEY') === '1') {
            $userSettings = $user->chatbotSettings;
            if ($userSettings) {
                $userKey = $provider === 'openai'
                    ? $userSettings->openai_api_key
                    : $userSettings->gemini_api_key;

                if ($userKey) {
                    try {
                        return decrypt($userKey);
                    } catch (\Exception $e) {
                        // Fall through to admin key
                    }
                }
            }
        }

        $settingKey = $provider === 'openai' ? 'OPENAI_API_KEY' : 'GEMINI_API_KEY';
        $adminKey = ChatbotSetting::get($settingKey);

        if ($adminKey) {
            try {
                return decrypt($adminKey);
            } catch (\Exception $e) {
                return $adminKey;
            }
        }

        return null;
    }

    /**
     * Send a message in a conversation and return the AI response.
     */
    public function sendMessage(User $user, int $conversationId, string $message, ?string $modelOverride = null, array $attachments = []): array
    {
        $conversation = ChatConversation::where('id', $conversationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // If user selected a specific model, determine provider from model name
        if ($modelOverride) {
            $provider = str_starts_with($modelOverride, 'gpt') || str_starts_with($modelOverride, 'dall-e') || str_starts_with($modelOverride, 'o') ? 'openai' : 'gemini';
            $model = $modelOverride;
        } else {
            $provider = $this->resolveProvider($user);
            $model = $this->resolveModel($provider);
        }

        // For vision, upgrade to a vision-capable model if images are attached
        $hasImages = collect($attachments)->contains('is_image', true);
        if ($hasImages && $provider === 'openai' && in_array($model, ['gpt-3.5-turbo'])) {
            $model = 'gpt-4o-mini'; // Upgrade to vision-capable model
        }

        $apiKey = $this->resolveApiKey($user, $provider);

        if (!$apiKey) {
            throw new \RuntimeException('API key not configured for this provider.');
        }

        // Save user message with attachments
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $message,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        $messages = $this->buildMessages($conversation, $attachments, $message);

        $reply = match ($provider) {
            'gemini' => $this->callGemini($apiKey, $messages, $model, $hasImages),
            default => $this->callOpenAI($apiKey, $messages, $model),
        };

        // Check if AI response contains generated images (for Gemini image generation)
        $generatedImages = [];
        if (preg_match_all('/\[generated_image:(.*?)\]/', $reply, $matches)) {
            $generatedImages = $matches[1];
            $reply = preg_replace('/\[generated_image:.*?\]/', '', $reply);
        }

        $assistantAttachments = !empty($generatedImages) ? array_map(fn($url) => [
            'url' => $url, 'is_image' => true, 'name' => 'Generated Image', 'mime' => 'image/png',
        ], $generatedImages) : null;

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $reply,
            'attachments' => $assistantAttachments,
        ]);

        if ($conversation->provider !== $provider) {
            $conversation->update(['provider' => $provider]);
        }

        return ['reply' => $reply, 'model' => $model, 'generated_images' => $generatedImages];
    }

    /**
     * Generate an image using DALL-E (OpenAI) or Imagen (Gemini).
     */
    public function generateImage(User $user, int $conversationId, string $prompt, ?string $modelOverride = null): array
    {
        $conversation = ChatConversation::where('id', $conversationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $provider = $modelOverride ? (str_starts_with($modelOverride, 'dall-e') ? 'openai' : 'gemini') : $this->resolveProvider($user);
        $apiKey = $this->resolveApiKey($user, $provider);

        if (!$apiKey) {
            throw new \RuntimeException('API key not configured for this provider.');
        }

        // Save user prompt
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => '🎨 Generate image: ' . $prompt,
        ]);

        if ($provider === 'openai') {
            $imageUrl = $this->callDallE($apiKey, $prompt);
        } else {
            $imageUrl = $this->callGeminiImageGeneration($apiKey, $prompt);
        }

        // Download and store the generated image
        $storedUrl = $imageUrl;
        try {
            $imageContent = Http::timeout(30)->get($imageUrl)->body();
            $filename = 'chatbot-generated/' . uniqid() . '.png';
            Storage::disk('public')->put($filename, $imageContent);
            $storedUrl = Storage::disk('public')->url($filename);
        } catch (\Exception $e) {
            // Use original URL if storage fails
        }

        $attachments = [[
            'url' => $storedUrl,
            'is_image' => true,
            'name' => 'Generated: ' . mb_substr($prompt, 0, 50),
            'mime' => 'image/png',
        ]];

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Here is the generated image for: "' . $prompt . '"',
            'attachments' => $attachments,
        ]);

        return [
            'image_url' => $storedUrl,
            'reply' => 'Here is the generated image for: "' . $prompt . '"',
            'attachments' => $attachments,
        ];
    }

    /**
     * Build the message array for the AI API call.
     */
    protected function buildMessages(ChatConversation $conversation, array $currentAttachments = [], string $currentMessage = ''): array
    {
        $messages = [];

        $systemPrompt = ChatbotSetting::get('CHATBOT_SYSTEM_PROMPT', 'You are a helpful assistant.');
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];

        $documents = ChatbotDocument::where('is_active', true)->get();
        if ($documents->isNotEmpty()) {
            $knowledgeContext = "Use the following knowledge base to help answer questions:\n\n";
            foreach ($documents as $doc) {
                $knowledgeContext .= "--- {$doc->title} ---\n{$doc->content}\n\n";
            }
            $messages[] = ['role' => 'system', 'content' => $knowledgeContext];
        }

        $history = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('id', 'desc')
            ->take(20)
            ->get()
            ->reverse();

        foreach ($history as $msg) {
            // Check if this message has image attachments
            $hasImageAttachments = false;
            $imageAttachments = [];
            if (!empty($msg->attachments)) {
                foreach ($msg->attachments as $att) {
                    if (!empty($att['is_image'])) {
                        $hasImageAttachments = true;
                        $imageAttachments[] = $att;
                    }
                }
            }

            if ($hasImageAttachments && $msg->role === 'user') {
                // Build multimodal content for messages with images
                $content = [];
                if ($msg->content) {
                    $content[] = ['type' => 'text', 'text' => $msg->content];
                }
                foreach ($imageAttachments as $att) {
                    $imageUrl = $att['url'];
                    // Try to get base64 from local storage
                    if (!empty($att['path']) && Storage::disk('public')->exists($att['path'])) {
                        $imageData = base64_encode(Storage::disk('public')->get($att['path']));
                        $mime = $att['mime'] ?? 'image/jpeg';
                        $content[] = [
                            'type' => 'image_url',
                            'image_url' => ['url' => "data:{$mime};base64,{$imageData}"],
                        ];
                    } else {
                        $content[] = [
                            'type' => 'image_url',
                            'image_url' => ['url' => $imageUrl],
                        ];
                    }
                }
                $messages[] = ['role' => $msg->role, 'content' => $content];
            } else {
                $messages[] = ['role' => $msg->role, 'content' => $msg->content];
            }
        }

        return $messages;
    }

    /**
     * Call the OpenAI Chat Completions API (supports vision with multimodal content).
     */
    public function callOpenAI(string $apiKey, array $messages, string $model = 'gpt-4o-mini'): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => 4096,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error from OpenAI.');
            throw new \RuntimeException('OpenAI API error: ' . $error);
        }

        return $response->json('choices.0.message.content', '');
    }

    /**
     * Call the Gemini generateContent API (supports vision with inline images).
     */
    public function callGemini(string $apiKey, array $messages, string $model = 'gemini-2.5-flash', bool $hasImages = false): string
    {
        $systemInstruction = null;
        $contents = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemInstruction = ($systemInstruction ? $systemInstruction . "\n\n" : '') .
                    (is_array($msg['content']) ? collect($msg['content'])->where('type', 'text')->pluck('text')->implode("\n") : $msg['content']);
                continue;
            }

            $role = $msg['role'] === 'assistant' ? 'model' : 'user';

            if (is_array($msg['content'])) {
                // Multimodal content (text + images)
                $parts = [];
                foreach ($msg['content'] as $part) {
                    if ($part['type'] === 'text') {
                        $parts[] = ['text' => $part['text']];
                    } elseif ($part['type'] === 'image_url') {
                        $imageUrl = $part['image_url']['url'];
                        if (str_starts_with($imageUrl, 'data:')) {
                            // Parse data URI
                            preg_match('/^data:(.*?);base64,(.*)$/', $imageUrl, $m);
                            if ($m) {
                                $parts[] = [
                                    'inline_data' => [
                                        'mime_type' => $m[1],
                                        'data' => $m[2],
                                    ],
                                ];
                            }
                        } else {
                            // Download and inline the image for Gemini
                            try {
                                $imgContent = Http::timeout(15)->get($imageUrl)->body();
                                $parts[] = [
                                    'inline_data' => [
                                        'mime_type' => 'image/jpeg',
                                        'data' => base64_encode($imgContent),
                                    ],
                                ];
                            } catch (\Exception $e) {
                                $parts[] = ['text' => '[Image could not be loaded]'];
                            }
                        }
                    }
                }
                $contents[] = ['role' => $role, 'parts' => $parts];
            } else {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $msg['content']]],
                ];
            }
        }

        $body = ['contents' => $contents];

        if ($systemInstruction) {
            $body['systemInstruction'] = [
                'parts' => [['text' => $systemInstruction]],
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(120)->post($url, $body);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error from Gemini.');
            throw new \RuntimeException('Gemini API error: ' . $error);
        }

        return $response->json('candidates.0.content.parts.0.text', '');
    }

    /**
     * Call DALL-E API for image generation.
     */
    protected function callDallE(string $apiKey, string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/images/generations', [
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
            'response_format' => 'url',
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to generate image.');
            throw new \RuntimeException('DALL-E error: ' . $error);
        }

        return $response->json('data.0.url', '');
    }

    /**
     * Call Gemini Imagen API for image generation.
     */
    protected function callGeminiImageGeneration(string $apiKey, string $prompt): string
    {
        // Use Gemini's imagen model for generation
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(120)->post("https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-002:predict?key={$apiKey}", [
            'instances' => [['prompt' => $prompt]],
            'parameters' => ['sampleCount' => 1],
        ]);

        if ($response->failed()) {
            // Fallback: use gemini-2.0-flash with image generation capability
            $fallbackResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(120)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => "Generate an image of: {$prompt}"]]]],
                'generationConfig' => ['responseModalities' => ['TEXT', 'IMAGE']],
            ]);

            if ($fallbackResponse->failed()) {
                $error = $response->json('error.message', 'Failed to generate image with Gemini.');
                throw new \RuntimeException('Gemini Image Generation error: ' . $error);
            }

            // Extract image from response
            $parts = $fallbackResponse->json('candidates.0.content.parts', []);
            foreach ($parts as $part) {
                if (isset($part['inlineData'])) {
                    $imageData = $part['inlineData']['data'];
                    $mime = $part['inlineData']['mimeType'] ?? 'image/png';
                    $filename = 'chatbot-generated/' . uniqid() . '.png';
                    Storage::disk('public')->put($filename, base64_decode($imageData));
                    return Storage::disk('public')->url($filename);
                }
            }

            throw new \RuntimeException('No image was generated. Try a different prompt.');
        }

        // Extract base64 image from Imagen response
        $imageData = $response->json('predictions.0.bytesBase64Encoded', '');
        if ($imageData) {
            $filename = 'chatbot-generated/' . uniqid() . '.png';
            Storage::disk('public')->put($filename, base64_decode($imageData));
            return Storage::disk('public')->url($filename);
        }

        throw new \RuntimeException('No image was generated.');
    }

    /**
     * Test an API key by making a minimal request.
     */
    public function testApiKey(string $provider, string $apiKey): array
    {
        try {
            if ($provider === 'openai') {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->timeout(15)->get('https://api.openai.com/v1/models');

                if ($response->failed()) {
                    return ['success' => false, 'error' => $response->json('error.message', 'Invalid API key or request failed.')];
                }

                return ['success' => true, 'message' => 'OpenAI API key is valid.'];
            }

            if ($provider === 'gemini') {
                $response = Http::timeout(15)->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

                if ($response->failed()) {
                    return ['success' => false, 'error' => $response->json('error.message', 'Invalid API key or request failed.')];
                }

                return ['success' => true, 'message' => 'Gemini API key is valid.'];
            }

            return ['success' => false, 'error' => 'Unknown provider.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Connection error: ' . $e->getMessage()];
        }
    }

    /**
     * List available models for a provider using the API key.
     */
    public function listModels(string $provider, string $apiKey): array
    {
        try {
            if ($provider === 'openai') {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->timeout(15)->get('https://api.openai.com/v1/models');

                if ($response->failed()) {
                    return [];
                }

                $models = collect($response->json('data', []))
                    ->filter(fn($m) => str_starts_with($m['id'], 'gpt-') || str_starts_with($m['id'], 'o'))
                    ->filter(fn($m) => !str_contains($m['id'], 'instruct') && !str_contains($m['id'], 'realtime') && !str_contains($m['id'], 'audio') && !str_contains($m['id'], 'search'))
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->toArray();

                return $models;
            }

            if ($provider === 'gemini') {
                $response = Http::timeout(15)->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

                if ($response->failed()) {
                    return [];
                }

                $models = collect($response->json('models', []))
                    ->filter(fn($m) => str_contains($m['name'] ?? '', 'gemini') && in_array('generateContent', $m['supportedGenerationMethods'] ?? []))
                    ->map(fn($m) => str_replace('models/', '', $m['name']))
                    ->sort()
                    ->values()
                    ->toArray();

                return $models;
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if a user has an API key set for a given provider.
     */
    protected function userHasKeyForProvider($userSettings, string $provider): bool
    {
        return match ($provider) {
            'openai' => !empty($userSettings->openai_api_key),
            'gemini' => !empty($userSettings->gemini_api_key),
            default => false,
        };
    }
}
