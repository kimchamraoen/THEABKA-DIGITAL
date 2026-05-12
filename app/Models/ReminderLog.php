<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $fillable = [
        'template_id',
        'days_left',
        'chat_id',
    ];

    public $timestamps = false;
}
