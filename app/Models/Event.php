<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'event_time',
        'title'
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
