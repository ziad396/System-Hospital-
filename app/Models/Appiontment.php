<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appiontment extends Model
{
    //
    protected $fillable=[
        'time',
        'day',
        'doctor_id',
        'user_id',
    ];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);  
    }
    public function user()
    {
        return $this->belongsTo(User::class);  
    }
}
