<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Guest;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $guests = Guest::whereNotNull('telegram_id')->get();

        foreach ($guests as $guest) {
            app(\App\Services\TelegramService::class)
                ->sendMessage($guest->telegram_id, "Reminder: Event is coming soon 🎉");
        }
    }
}
