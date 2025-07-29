<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;


class AppointmentStatus extends Model
{
 

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'appointment_status';
    protected $fillable = ['value', 'label', 'sequence', 'status'];
    
  
}
