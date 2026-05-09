<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $fillable = [
        'day',
        'start_time',
        'end_time',
        'doctor_id',
    ];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
