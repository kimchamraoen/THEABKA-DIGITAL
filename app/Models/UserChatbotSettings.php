<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChatbotSettings extends Model
{
    protected $table = 'user_chatbot_settings';

    protected $fillable = ['user_id', 'provider', 'openai_api_key', 'gemini_api_key'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
