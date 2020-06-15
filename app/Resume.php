<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resume extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function postulation()
    {
        return $this->hasOne(Postulation::class);
    }

    protected $fillable = [
        'user_id', 'client_id', 'name', 'url', 'size'
    ];

    public function getUrlAttribute($value)
    {
        return [
            'url' => Storage::url($value),
            'path' => $value
        ];
    }
}
