<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function postulations()
    {
        return $this->hasMany(Postulation::class);
    }

    public function getDescriptionAttribute($value){
        return json_decode($value);
    }


    protected $fillable = [
        'client_id', 'due_date', 'title', 'description', 'salary', 'contract_type', 
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];
}
