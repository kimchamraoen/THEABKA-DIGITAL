<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TelegramGroupSession;

class TelegramConnectController extends Controller
{
    public function connectTelegram($templateId)
    {
        // DELETE OLD PENDING SESSIONS
        TelegramGroupSession::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->delete();

        // CREATE NEW SESSION
        TelegramGroupSession::create([
            'user_id' => auth()->id(),
            'template_id' => $templateId,
            'status' => 'pending',
        ]);

        return redirect()->back()->with(
            'success',
            'Now add the bot to your Telegram group.'
        );
    }
}