<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use function GuzzleHttp\json_decode;

class Postulation extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function resume()
    {
        return $this->hasOne(Resume::class);
    }

    protected $fillable = [
        'resume_id', 'job_id', 'user_id', 'cover_letter', 'client_id', 
    ];

    public function getCoverLetterAttribute($value)
    {
        return json_decode($value);
    }
}
