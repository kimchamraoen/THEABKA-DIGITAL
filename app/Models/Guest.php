<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'user_id',
        'guest_name',
        'phone',
        'group',
        'Greeting',
        'note',
        'gift_money',
        'gift',
        'statue',
        'uuid',
        'template_id',
        'telegram_chat_id',
        'telegram_account'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function template()
    // {
    //     return $this->belongsTo(Template::class);
    // }
}
