<?php

namespace App\Console\Commands;

use App\Models\ReminderLog;
use App\Models\TelegramGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeddingReminder extends Command
{
    protected $signature = 'wedding:reminder';

    protected $description = 'Send wedding reminders automatically (multi-user safe)';

    public function handle()
    {
        $today = Carbon::now('Asia/Phnom_Penh')->startOfDay();


        // FIX: ONLY load groups (not templates)
        // This ensures multi-user safety
        $groups = TelegramGroup::with('template')
            ->whereHas('template', function ($q) {
                $q->whereNotNull('date');
            })
            ->get();

        if ($groups->isEmpty()) {
            $this->warn("No Telegram groups found.");
            return Command::SUCCESS;
        }

        foreach ($groups as $group) {

            $template = $group->template;

            if (!$template || !$template->date) {
                continue;
            }

            try {

                $weddingDate = Carbon::parse($template->date)
                    ->timezone('Asia/Phnom_Penh')
                    ->startOfDay();

                $daysLeft = $today->diffInDays($weddingDate, false);

                $this->info("=================================");
                $this->info("Group ID: {$group->chat_id}");
                $this->info("Template ID: {$template->id}");
                $this->info("Days Left: {$daysLeft}");

                // Skip past wedding
                if ($daysLeft < 0) {
                    $this->warn("Wedding already passed.");
                    continue;
                }

                // Allowed reminder days
                $allowedDays = [30, 15, 7, 3, 2, 1, 0];

                if (!in_array($daysLeft, $allowedDays)) {
                    $this->warn("No reminder needed today.");
                    continue;
                }

                
                // FIX: Prevent duplicate per GROUP + TEMPLATE + DAY
               
                $alreadySent = ReminderLog::where([
                    'template_id' => $template->id,
                    'chat_id' => $group->chat_id,
                    'days_left' => $daysLeft,
                ])->exists();

                if ($alreadySent) {
                    $this->warn("Already sent for this group.");
                    continue;
                }

                $message = $this->getMessageByDays($daysLeft);

                if (!$message) {
                    $this->error("Message not found.");
                    continue;
                }

                $this->info("Sending message...");

                $success = $this->sendTelegram($group->chat_id, $message);

                if ($success) {

                    ReminderLog::create([
                        'template_id' => $template->id,
                        'chat_id' => $group->chat_id,
                        'days_left' => $daysLeft,
                        'sent_at' => now(),
                    ]);

                    $this->info("SENT SUCCESS");
                } else {
                    $this->error("FAILED SENDING");
                }

            } catch (\Exception $e) {

                Log::error("WeddingReminder Error", [
                    'group_id' => $group->id,
                    'template_id' => $template->id ?? null,
                    'message' => $e->getMessage(),
                ]);

                $this->error($e->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    // MESSAGES
    private function getMessageByDays($days)
    {
        $days = (int) round($days);
        
        return match ($days) {

            30 => "💖 អតិថិជនជាទីគោរពស្រឡាញ់ 💖
🎉 អាពាហ៍ពិពាហ៍នៅសល់ 30 ថ្ងៃ!
💡 សូមចាប់ផ្តើមរៀបចំទុកជាមុន។",

            15 => "💖 នៅសល់ 15 ថ្ងៃ!
💍 ពិនិត្យការរៀបចំទាំងអស់។",

            7 => "⏳ នៅសល់ 7 ថ្ងៃ!
💡 ពិនិត្យចុងក្រោយ។",

            3 => "🔥 នៅសល់ 3 ថ្ងៃ!
💍 ត្រៀមខ្លួនឲ្យរួចរាល់។",

            2 => "⚡ នៅសល់ 2 ថ្ងៃ!
💡 សម្រាក និងពិនិត្យឡើងវិញ។",

            1 => "💍 ថ្ងៃស្អែកជាថ្ងៃអាពាហ៍ពិពាហ៍!",

            0 => "🎉💍 ថ្ងៃនេះជាថ្ងៃអាពាហ៍ពិពាហ៍!
សូមអបអរសាទរ 💖",

            default => null,
        };
    }


    // SEND TELEGRAM
    private function sendTelegram($chatId, $message)
    {
        try {

            $token = env('TELEGRAM_BOT_TOKEN');

            $response = Http::timeout(15)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);

            $result = $response->json();

            Log::info("Telegram Response", [
                'chat_id' => $chatId,
                'response' => $result,
            ]);

            return $response->successful() && ($result['ok'] ?? false);

        } catch (\Exception $e) {

            Log::error("Telegram Send Failed", [
                'chat_id' => $chatId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}