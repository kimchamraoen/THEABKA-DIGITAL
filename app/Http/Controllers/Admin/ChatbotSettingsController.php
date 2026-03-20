<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotDocument;
use App\Models\ChatbotSetting;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotSettingsController extends Controller
{
    public function __construct(protected ChatbotService $chatbotService) {}

    /**
     * Show the admin chatbot settings form.
     */
    public function index(): View
    {
        $settings = [];
        $keys = [
            'OPENAI_API_KEY', 'GEMINI_API_KEY', 'CHATBOT_DEFAULT_PROVIDER',
            'CHATBOT_ENABLED', 'CHATBOT_ALLOW_USER_API_KEY', 'CHATBOT_SYSTEM_PROMPT',
            'OPENAI_MODEL', 'GEMINI_MODEL',
        ];

        foreach ($keys as $key) {
            $record = ChatbotSetting::where('key', $key)->first();
            $settings[$key] = $record?->value ?? '';
        }

        $documents = ChatbotDocument::orderByDesc('created_at')->get();

        return view('admin.chatbot.index', compact('settings', 'documents'));
    }

    /**
     * Save all chatbot settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'CHATBOT_DEFAULT_PROVIDER' => 'required|in:auto,openai,gemini',
            'CHATBOT_ENABLED' => 'nullable',
            'CHATBOT_ALLOW_USER_API_KEY' => 'nullable',
            'CHATBOT_SYSTEM_PROMPT' => 'nullable|string|max:5000',
            'OPENAI_MODEL' => 'nullable|string|max:100',
            'GEMINI_MODEL' => 'nullable|string|max:100',
        ]);

        foreach (['OPENAI_API_KEY', 'GEMINI_API_KEY'] as $keyName) {
            if ($request->filled($keyName)) {
                ChatbotSetting::set($keyName, encrypt($request->input($keyName)));
            }
            if ($request->has('clear_' . $keyName)) {
                ChatbotSetting::set($keyName, null);
            }
        }

        ChatbotSetting::set('CHATBOT_DEFAULT_PROVIDER', $request->input('CHATBOT_DEFAULT_PROVIDER'));
        ChatbotSetting::set('CHATBOT_ENABLED', $request->has('CHATBOT_ENABLED') ? '1' : '0');
        ChatbotSetting::set('CHATBOT_ALLOW_USER_API_KEY', $request->has('CHATBOT_ALLOW_USER_API_KEY') ? '1' : '0');
        ChatbotSetting::set('CHATBOT_SYSTEM_PROMPT', $request->input('CHATBOT_SYSTEM_PROMPT'));
        ChatbotSetting::set('OPENAI_MODEL', $request->input('OPENAI_MODEL'));
        ChatbotSetting::set('GEMINI_MODEL', $request->input('GEMINI_MODEL'));

        return redirect()->back()->with('success', 'Chatbot settings saved.');
    }

    /**
     * Test an API key by making a minimal request.
     */
    public function testApi(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|in:openai,gemini',
            'api_key' => 'nullable|string|max:500',
        ]);

        $provider = $request->input('provider');
        $apiKey = $request->input('api_key');

        // If no key provided, use the stored one
        if (!$apiKey) {
            $settingKey = $provider === 'openai' ? 'OPENAI_API_KEY' : 'GEMINI_API_KEY';
            $stored = ChatbotSetting::get($settingKey);
            if ($stored) {
                try {
                    $apiKey = decrypt($stored);
                } catch (\Exception $e) {
                    $apiKey = $stored;
                }
            }
        }

        if (!$apiKey) {
            return response()->json(['success' => false, 'error' => 'No API key provided or stored.']);
        }

        $result = $this->chatbotService->testApiKey($provider, $apiKey);
        return response()->json($result);
    }

    /**
     * List available models for a provider.
     */
    public function listModels(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|in:openai,gemini',
            'api_key' => 'nullable|string|max:500',
        ]);

        $provider = $request->input('provider');
        $apiKey = $request->input('api_key');

        if (!$apiKey) {
            $settingKey = $provider === 'openai' ? 'OPENAI_API_KEY' : 'GEMINI_API_KEY';
            $stored = ChatbotSetting::get($settingKey);
            if ($stored) {
                try {
                    $apiKey = decrypt($stored);
                } catch (\Exception $e) {
                    $apiKey = $stored;
                }
            }
        }

        if (!$apiKey) {
            return response()->json(['models' => []]);
        }

        $models = $this->chatbotService->listModels($provider, $apiKey);
        return response()->json(['models' => $models]);
    }

    /**
     * List uploaded documents (JSON).
     */
    public function documents(): JsonResponse
    {
        $documents = ChatbotDocument::orderByDesc('created_at')->get();
        return response()->json(['documents' => $documents]);
    }

    /**
     * Upload a new document.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:100000',
        ]);

        ChatbotDocument::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Document uploaded.');
    }

    /**
     * Delete a document.
     */
    public function deleteDocument(int $id): RedirectResponse
    {
        ChatbotDocument::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Document deleted.');
    }

    /**
     * Toggle a document's active state.
     */
    public function toggleDocument(int $id): JsonResponse
    {
        $doc = ChatbotDocument::findOrFail($id);
        $doc->update(['is_active' => !$doc->is_active]);

        return response()->json(['is_active' => $doc->is_active]);
    }
}
