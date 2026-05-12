<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Models\Guest;
use App\Models\TelegramGroup;
use App\Models\TelegramGroupSession;

class TelegramController extends Controller
{
    private $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
    }

    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('Telegram Update:', $data);

        //  HANDLE BOT ADDED TO GROUP

        if (isset($data['my_chat_member'])) {

            $chat = $data['my_chat_member']['chat'] ?? null;
            $newStatus = $data['my_chat_member']['new_chat_member']['status'] ?? null;

            if (
                $chat &&
                in_array($chat['type'], ['group', 'supergroup']) &&
                in_array($newStatus, ['member', 'administrator'])
            ) {

                $chatId = $chat['id'];
                $chatTitle = $chat['title'] ?? 'Unnamed Group';

                $session = TelegramGroupSession::where('status', 'pending')
                    ->latest()
                    ->first();

                if (!$session) {
                    Log::warning('NO PENDING SESSION');
                    return response()->json(['ok' => true]);
                }

                TelegramGroup::updateOrCreate(
                    ['chat_id' => $chatId],
                    [
                        'name' => $chatTitle,
                        'user_id' => $session->user_id,
                        'template_id' => $session->template_id,
                    ]
                );

                $session->update([
                    'status' => 'active',
                    'chat_id' => $chatId
                ]);

                Log::info('GROUP SAVED', ['chat_id' => $chatId]);
            }

            return response()->json(['ok' => true]);
        }

        //  NORMAL MESSAGE

        $message = $data['message'] ?? null;

        if (!$message) return response()->json(['ok' => true]);

        $text = $message['text'] ?? '';
        if (!$text) return response()->json(['ok' => true]);

        $chatId = $message['chat']['id'] ?? null;

        $group = TelegramGroup::where('chat_id', $chatId)->first();

        if (!$group) {
            Log::warning('UNKNOWN GROUP', ['chat_id' => $chatId]);
            return response()->json(['ok' => true]);
        }

        //  CHECK PAYMENT MESSAGE

        if (!$this->isPaymentMessage($text)) {
            return response()->json(['ok' => true]);
        }

        // PARSE PAYMENT

        $payment = $this->parsePayment($text);

        Log::info('PAYMENT PARSED', $payment);

        if (empty($payment['amount']) || empty($payment['guest_name'])) {
            Log::warning('INVALID PAYMENT DATA', $payment);
            return response()->json(['ok' => true]);
        }

        // CLEAN GUEST NAME

        $guestName = $payment['guest_name'];

        // remove (*869), (869), etc
        $guestName = preg_replace('/\(\*?\d+\)/', '', $guestName);
        $guestName = trim($guestName);

        Log::info('CLEAN GUEST NAME', [
            'raw' => $payment['guest_name'],
            'clean' => $guestName
        ]);

        // FIND GUEST (SCOPED BY USER)
        $guest = Guest::where('user_id', $group->user_id)
            ->whereRaw('LOWER(guest_name) = ?', [
                strtolower($guestName)
            ])
            ->first();

        if (!$guest) {
            Log::warning('GUEST NOT FOUND', [
                'name' => $guestName,
                'user_id' => $group->user_id
            ]);
            return response()->json(['ok' => true]);
        }

        //  UPDATE GUEST PAYMENT

        $guest->gift_money += $payment['amount'];
        $guest->note = $payment['currency'];
        $guest->save();

        // $this->sendMessage(
        //     $chatId,
        //     "💰 Payment Received\n" .
        //     "Name: {$guest->guest_name}\n" .
        //     "Amount: {$payment['amount']} {$payment['currency']}"
        // );

        Log::info('PAYMENT SUCCESS', [
            'guest_id' => $guest->id,
            'amount' => $payment['amount']
        ]);

        return response()->json(['ok' => true]);
    }

    private function isPaymentMessage($text)
    {
        return (
            str_contains($text, '$') ||
            str_contains($text, '៛') ||
            str_contains($text, 'បង់ដោយ') ||
            str_contains($text, 'paid by') ||
            str_contains($text, 'ត្រូវបានបង់') ||
            str_contains($text, 'ABA')
        );
    }

    private function parsePayment($text)
    {
        $amount = null;
        $currency = null;
        $guest = null;

        // USD
        if (preg_match('/\$(\d+(\.\d+)?)/', $text, $m)) {
            $amount = (float) $m[1];
            $currency = 'USD';
        }

        // KHR
        if (preg_match('/(\d+(\.\d+)?)\s*៛/', $text, $m)) {
            $amount = (float) $m[1];
            $currency = 'KHR';
        }

        // English
        if (preg_match('/by\s+([^\(\n]+)/i', $text, $m)) {
            $guest = trim($m[1]);
        }

        // Khmer
        if (!$guest && preg_match('/ដោយ\s+([^\(\n]+)/u', $text, $m)) {
            $guest = trim($m[1]);
        }

        return [
            'amount' => $amount,
            'currency' => $currency,
            'guest_name' => $guest,
        ];
    }

    private function sendMessage($chatId, $message)
    {
        Http::post(
            "https://api.telegram.org/bot{$this->botToken}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $message,
            ]
        );
    }
}