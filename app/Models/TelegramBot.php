<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = [
        'user_id',
        'bot_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
