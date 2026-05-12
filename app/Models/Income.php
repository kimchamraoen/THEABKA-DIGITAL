<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'user_id',
        'name_guest',
        'amount_pay',
        'date_pay',
        'category_pay',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
