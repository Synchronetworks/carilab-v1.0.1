<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Appointment\Database\factories\LiveLocationFactory;
use Modules\Appointment\Models\Appointment;

class LiveLocation extends Model
{
   
    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'live_location';
    protected $fillable = [
        'appointment_id', 'latitude', 'longitude'
    ];

    protected $casts = [
        'latitude'=> 'double',
        'longitude'=> 'double',
        'appointment_id'    => 'integer',
    ];

    public function booking(){
        return $this->belongsTo(Appointment::class,'appointment_id','id');
    }
    
    protected static function newFactory(): LiveLocationFactory
    {
        //return LiveLocationFactory::new();
    }
}
