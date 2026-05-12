<?php

namespace App\Console\Commands;

use App\Models\Expence;
use App\Models\Expense;
use Illuminate\Console\Command;
use App\Models\Guest;
use App\Models\TelegramGroup;
use Illuminate\Support\Facades\Http;

class WeddingDailyReport extends Command
{
    protected $signature = 'template:report {time} {date}';
    protected $description = 'Send template report';

    public function handle()
{
    $time = $this->argument('time');
    $date = $this->argument('date');

    $groups = TelegramGroup::all();

    foreach ($groups as $group) {

        $userId = $group->user_id;
        $chatId = $group->chat_id;

        // SAFE CHECK
        if (!$userId || !$chatId) {
            continue;
        }

        // USER BASED DATA
        $guestCount = Guest::where('user_id', $userId)->count();

        $guestPaid = Guest::where('user_id', $userId)
            ->whereNotNull('gift_money')
            ->count();

        $dolla = Guest::where('user_id', $userId)->where('note', 'USD')
            ->sum('gift_money');

        $riel = Guest::where('user_id', $userId)->where('note', 'KHR')
            ->sum('gift_money');

        $convertedRiel = $riel / 4100;
        $sum = $dolla + $convertedRiel;

        $total = round($sum, 2);

        $expense = Expence::where('user_id', $userId)
            ->sum('amount');

        $profit = $total - $expense;

        $message =
            "Template Report ({$time})\n\n" .
            "Total Guests: {$guestCount}\n" .
            "Guests Paid: {$guestPaid}\n" .
            "Paid ($): {$dolla}$\n" .
            "Paid (៛): {$riel}៛\n" .
            "Total Paid: {$total}$\n" .
            "Expense: {$expense}$\n" .
            "Profit: {$profit}$";

        $this->sendTelegram($chatId, $message);

        $this->info("Sent to: {$chatId}");
    }

    return 0;
}

    private function sendTelegram($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        return Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}