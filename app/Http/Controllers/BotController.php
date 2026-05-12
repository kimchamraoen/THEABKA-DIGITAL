<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\TelegramBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramService;

class BotController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new TelegramService();
    }

    public function storeBot(Request $request)
    {
        $request->validate([
            'bot_token' => 'required'
        ]);

        TelegramBot::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'bot_token' => $request->bot_token
            ]
        );

        return back()->with('success', 'Bot saved successfully');
    }

    public function handle(Request $request)
    {
        $message = $request->input('message');

        if (!$message || !isset($message['chat']['id']) || !isset($message['text'])) {
            Log::warning('Invalid Telegram payload', ['payload' => $request->all()]);
            return response()->json(['status' => 'ignored']);
        }

        $chat_id = $message['chat']['id'];
        $text = trim($message['text']);

        if (str_starts_with($text, '/start')) {
            $this->handleStart($chat_id, $text);
        }

        // Ignore all other messages
        return response()->json(['status' => 'ok']);
    }

    private function handleStart($chat_id, $text)
    {
        // Extract UUID from /start command
        $uuid = trim(str_replace('/start', '', $text));

        $guest = Guest::where('uuid', $uuid)->first();

        if ($guest) {
            // Update Telegram chat ID
            $guest->update(['telegram_chat_id' => $chat_id]);

            // Generate personal RSVP link
            $link = route('landing-wedding', $guest->uuid);

            $message = "Hello {$guest->guest_name}! 💌\nClick the button below to open your personal RSVP link:";

            // Send message with inline button
            $this->sendMessageWithButton($chat_id, $message, "Open RSVP", $link);
        } else {
            $this->telegram->sendMessage($chat_id, "Sorry, we could not find your invitation. ❌");
        }
    }

    private function sendMessageWithButton($chat_id, $text, $button_text, $url)
    {
        $reply_markup = [
            'inline_keyboard' => [
                [
                    ['text' => $button_text, 'url' => $url]
                ]
            ]
        ];

        $this->telegram->sendMessageWithKeyboard($chat_id, $text, $reply_markup);
    }
}