<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'ip_address',
        'device_type',
        'browser',
        'os',
        'country',
        'region',
        'city',
        'latitude',
        'longitude',
        'logged_in_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'logged_in_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
