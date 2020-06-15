<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resume()
    {
        return $this->hasMany(Resume::class);
    }

    public function postulations()
    {
        return $this->hasMany(Postulation::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'description', 'slug', 'logo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id'
    ];

    public function getLogoAttribute($value)
    {
        if (!$value) {
            return $value;
        }
        return [
            'url' => Storage::url($value),
            'path' => $value
        ];
    }

    public function getDescriptionAttribute($value)
    {
        return json_decode($value);
    }
}
