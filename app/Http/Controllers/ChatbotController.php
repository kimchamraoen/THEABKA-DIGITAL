<?php

namespace App\Http\Controllers;

use App\Models\ChatbotSetting;
use App\Models\ChatConversation;
use App\Models\UserChatbotSettings;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function __construct(protected ChatbotService $chatbotService) {}

    /**
     * Show the chat UI with conversation list.
     */
    public function index(): View
    {
        $user = auth()->user();
        $conversations = ChatConversation::where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->get();

        $allowUserKey = ChatbotSetting::get('CHATBOT_ALLOW_USER_API_KEY') === '1';
        $userSettings = $user->chatbotSettings;
        $defaultProvider = ChatbotSetting::get('CHATBOT_DEFAULT_PROVIDER', 'auto');

        return view('chatbot.index', compact('conversations', 'allowUserKey', 'userSettings', 'defaultProvider'));
    }

    /**
     * Create a new conversation.
     */
    public function newConversation(): JsonResponse
    {
        $user = auth()->user();
        $provider = $this->chatbotService->resolveProvider($user);

        $conversation = ChatConversation::create([
            'user_id' => $user->id,
            'title' => 'New Chat',
            'provider' => $provider,
        ]);

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
        ]);
    }

    /**
     * Upload a file/image attachment for a chat message.
     */
    public function uploadAttachment(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480|mimes:jpg,jpeg,png,gif,webp,pdf,txt,csv,md,doc,docx',
        ]);

        $file = $request->file('file');
        $path = $file->store('chatbot-attachments', 'public');
        $mime = $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'name' => $file->getClientOriginalName(),
            'mime' => $mime,
            'is_image' => $isImage,
            'size' => $file->getSize(),
        ]);
    }

    /**
     * Send a message in a conversation (with optional attachments).
     */
    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'message' => 'nullable|string|max:10000',
            'model' => 'nullable|string|max:100',
            'attachments' => 'nullable|array|max:5',
            'attachments.*.path' => 'required_with:attachments|string',
            'attachments.*.name' => 'required_with:attachments|string',
            'attachments.*.mime' => 'required_with:attachments|string',
            'attachments.*.is_image' => 'required_with:attachments|boolean',
            'attachments.*.url' => 'required_with:attachments|string',
        ]);

        $message = $request->input('message', '');
        $attachments = $request->input('attachments', []);

        if (empty($message) && empty($attachments)) {
            return response()->json(['error' => 'Please provide a message or attachment.'], 422);
        }

        try {
            $modelOverride = $request->input('model');
            $result = $this->chatbotService->sendMessage(
                auth()->user(), $id, $message, $modelOverride, $attachments
            );

            // Update conversation title from first message
            $conversation = ChatConversation::find($id);
            if ($conversation && $conversation->title === 'New Chat') {
                $titleSource = $message ?: 'Image conversation';
                $conversation->update([
                    'title' => mb_substr($titleSource, 0, 50) . (mb_strlen($titleSource) > 50 ? '...' : ''),
                ]);
            }

            return response()->json([
                'reply' => $result['reply'],
                'model' => $result['model'],
                'title' => $conversation?->fresh()?->title,
                'generated_images' => $result['generated_images'] ?? [],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Generate an image using AI.
     */
    public function generateImage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:4000',
            'model' => 'nullable|string|max:100',
        ]);

        try {
            $result = $this->chatbotService->generateImage(
                auth()->user(), $id, $request->input('prompt'), $request->input('model')
            );

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Get messages for a conversation.
     */
    public function getMessages(int $id): JsonResponse
    {
        $conversation = ChatConversation::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $messages = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('id')
            ->get(['role', 'content', 'attachments', 'created_at']);

        return response()->json(['messages' => $messages]);
    }

    /**
     * Delete a conversation.
     */
    public function deleteConversation(int $id): JsonResponse
    {
        $conversation = ChatConversation::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Clean up attachment files
        foreach ($conversation->messages as $msg) {
            if (!empty($msg->attachments)) {
                foreach ($msg->attachments as $att) {
                    if (!empty($att['path'])) {
                        Storage::disk('public')->delete($att['path']);
                    }
                }
            }
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update the user's own chatbot settings (API keys, provider).
     */
    public function updateUserSettings(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'nullable|in:openai,gemini',
            'openai_api_key' => 'nullable|string|max:500',
            'gemini_api_key' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $settings = UserChatbotSettings::firstOrCreate(['user_id' => $user->id]);

        $data = ['provider' => $request->input('provider')];

        if ($request->filled('openai_api_key')) {
            $data['openai_api_key'] = encrypt($request->input('openai_api_key'));
        }
        if ($request->filled('gemini_api_key')) {
            $data['gemini_api_key'] = encrypt($request->input('gemini_api_key'));
        }

        if ($request->has('openai_api_key') && $request->input('openai_api_key') === '') {
            $data['openai_api_key'] = null;
        }
        if ($request->has('gemini_api_key') && $request->input('gemini_api_key') === '') {
            $data['gemini_api_key'] = null;
        }

        $settings->update($data);

        return response()->json(['success' => true]);
    }
}
