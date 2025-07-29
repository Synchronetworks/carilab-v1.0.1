<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Modules\Appointment\Models\Appointment;
use Illuminate\Database\Eloquent\SoftDeletes;
class AppointmentCollectorMapping extends Model
{
   
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['appointment_id','collector_id'];
    protected $table = 'appointment_collector_mapping';
    
   

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class)->with('userCommissionMapping');
    }
}
