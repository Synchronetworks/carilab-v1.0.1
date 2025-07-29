<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;

use Modules\Appointment\Models\Appointment;

class AppointmentActivity extends Model
{
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['datetime', 'appointment_id', 'activity_type', 'activity_message', 'activity_data','activity_date'];
    
    
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
