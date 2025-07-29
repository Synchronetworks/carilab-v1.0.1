<?php

namespace Modules\Appointment\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Lab\Models\Lab;
use App\Models\User;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Appointment\Models\AppointmentCollectorMapping;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Appointment\Models\AppointmentOtpMapping;
use Modules\Commision\Models\CommissionEarning;
use App\Models\UserOtherMapping;
use App\Models\UserAddressMapping;
class Appointment extends BaseModel
{
  
    use SoftDeletes;

    protected $table = 'appointments';
    protected $fillable = ['status', 'customer_id','other_member_id', 'vendor_id', 
    'lab_id', 'test_type', 'test_id', 'collection_type', 'amount', 
    'test_discount_amount', 'total_amount', 'appointment_date', 'appointment_time','address_id','submission_status','rejected_id','by_suggestion','cancellation_reason','symptoms','reschedule_reason'];
    protected $casts = [
        'amount' => 'double',
        'test_discount_amount' => 'double',
        'total_amount' => 'double',
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Appointment\Models\Appointment';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */


    protected $appends = ['feature_image'];

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && ! empty($media) ? $media : '';
    }
    
    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
    public function customer()
    {
        return $this->belongsTo(User::class)->with('otherMapping');
    }
    public function othermember()
    {
        return $this->belongsTo(UserOtherMapping::class,'other_member_id');
    }
    public function address()
    {
        return $this->belongsTo(UserAddressMapping::class,'address_id','id');
    }
    public function vendor()
    {
        return $this->belongsTo(User::class);
    }
    public function catlog()
    {
        return $this->belongsTo(CatlogManagement::class, 'test_id');
    }
    public function package()
    {
        return $this->belongsTo(PackageManagement::class, 'test_id');
    }
    public function getTestAttribute()
    {
        if ($this->test_type === 'test_package') {
            return $this->package;
        }
        return $this->catlog;
    }
    public function appointmentCollectorMapping()
    {
        return $this->hasOne(AppointmentCollectorMapping::class)->with('collector');
    }

    public function transactions()
    {
        return $this->hasOne(AppointmentTransaction::class);
    }
    public function cashhistory()
    {
        return $this->hasMany('Modules\Appointment\Models\CashPaymentHistories', 'appointment_id');
    }
    public function commissionsdata()
    {
        return $this->hasMany(CommissionEarning::class, 'commissionable_id', 'id');
    }
    public function otpMapping()
    {
        return $this->hasOne(AppointmentOtpMapping::class);
    }
    public function scopeMyAppointment($query){
        $user = auth()->user();
       
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed();
        }

        if($user->hasRole('vendor')) {
            return $query->where('vendor_id', $user->id);
        }

        if($user->hasRole('user')) {
            return $query->where('customer_id', $user->id);
        }

        if($user->hasRole('collector')) {
            return $query->whereHas('appointmentCollectorMapping',function ($q) use($user){
                $q->where('collector_id',$user->id);
            });
        }

        return $query;
    }
    public function commission()
    {
        return $this->morphMany(CommissionEarning::class, 'commissionable');
    }

    public function getMedicalReportAttribute()
    {
        return $this->getFirstMediaUrl('medical_report') ? setBaseUrlWithFileName($this->getFirstMediaUrl('medical_report')) : null ;
    }

}
