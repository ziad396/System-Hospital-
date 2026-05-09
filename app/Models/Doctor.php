<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Doctor extends Model
{
    //
    protected $fillable=[
        'name','specialization_id','phone','user_id'
    ];
      public function specialzation(){
        return $this->belongsTo(Specialziation::class);
    }
   
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }
    public function appiontment()
    {
        return $this->hasMany(Appiontment::class);
    }
}
