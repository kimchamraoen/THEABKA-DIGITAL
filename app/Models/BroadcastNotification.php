<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BroadcastNotification extends Model
{
    protected $fillable = [
        'sender_id',
        'title',
        'message',
        'target_role',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'broadcast_notification_user')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
