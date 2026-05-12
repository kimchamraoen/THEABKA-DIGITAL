<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    // Send plain message
    public function sendMessage($chat_id, $text)
    {
        try {
            Http::post($this->apiUrl . 'sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            Log::error("Telegram sendMessage failed: " . $e->getMessage());
        }
    }

    // Send message with button
    public function sendMessageWithKeyboard($chat_id, $text, $reply_markup)
    {
        try {
            Http::post($this->apiUrl . 'sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $reply_markup
            ]);
        } catch (\Exception $e) {
            Log::error("Telegram sendMessageWithKeyboard failed: " . $e->getMessage());
        }
    }
}