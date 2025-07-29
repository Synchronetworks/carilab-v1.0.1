<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentTransaction extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['appointment_id','txn_id','discount_type','discount_value','discount_amount','coupon_id','coupon','coupon_amount','tax','total_tax_amount','total_amount','payment_type','payment_status','request_token'];

    protected $table = 'appointment_transaction';
    protected $casts = [
        'tax' => 'array',
        'coupon' => 'array',
        'discount_amount' => 'double',
        'total_tax_amount' => 'double',
        'total_amount' => 'double',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class)->with(['appointmentCollectorMapping', 'otpMapping','commissionsdata']);
    }
    public function cashhistory()
    {
        return $this->hasMany('Modules\Appointment\Models\CashPaymentHistories', 'transaction_id');
    }
    
    public function scopeMyPayment($query)
    {
        $user = auth()->user();
        if($user->hasAnyRole(['admin', 'demo_admin'])){
            return $query->withTrashed();
        }

        if($user->hasRole('collector')) {
            return $query->whereHas('appointment', function($q) use($user) {
                $q->whereHas('appointmentCollectorMapping', function($q) use($user) {
                    $q->where('collector_id', '=', $user->id);
                });
            });
        }

        if($user->hasRole('user')) {
            return $query->whereHas('appointment', function($q) use($user) {
                $q->where('customer_id', '=', $user->id);
            });
        }

        if($user->hasRole('vendor')) {
            return $query->whereHas('appointment',function ($q) use($user) {
                    $q->where('vendor_id',$user->id);
            });
        }

        return $query;
    }
}
