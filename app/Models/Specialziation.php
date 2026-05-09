<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialziation extends Model
{
    //

    protected $fillable=[
    'name',
    'phone',
    'specialization_id',
    ];
        protected $table = 'specializations';

    public function doctor(){
        return $this->hasMany(Doctor::class);
    }
}
