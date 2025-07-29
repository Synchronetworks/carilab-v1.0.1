<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Modules\Appointment\Models\Appointment;
use Illuminate\Database\Eloquent\SoftDeletes;
class AppointmentOtpMapping extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['appointment_id', 'collector_id', 'otp','otp_generated_at'];

    protected $table = 'appointment_otp_mapping';
    protected $casts = [
        'otp_generated_at' => 'datetime',
    ];
 

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class);
    }
}
