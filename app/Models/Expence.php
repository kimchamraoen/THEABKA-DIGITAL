<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expence extends Model
{
    protected $table = 'exspences';

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'date',
        'category',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
