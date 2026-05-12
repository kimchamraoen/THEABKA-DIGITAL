<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'user_id',
        'title_font_family',
        'text_font_family',
        'title_color',
        'text_color',
        'text_font_size',
        'background_images',
        'background_music',
        'bride_name',
        'groom_name',
        'date',
        'event_time',
        'title',
        'subtitle',
        'address',
        'title_invitation',
        'message_invitation',
        'title_thanks',
        'message_thanks',
        'link_map',
        'map_photo',
        'dollar_qr',
        'khmer_qr',
        'pre_wedding1',
        'pre_wedding2',
        'pre_wedding3',
        'pre_wedding4',
        'cover_image',
        'video_url',
        'video_public_id',
        'event',
        'option',
        'type',
        'facebook',
        'instagram',
        'telegram',
        'phone',
        'link_dollar',
        'link_khmer'
    ];

    protected $casts = [
        'background_images' => 'array',
        'background_music' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
