<?php

namespace Modules\Commision\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Appointment\Models\Appointment;

class CommissionEarning extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['employee_id','commissionable','commissions','user_type','model_id', 'commission_amount','commission_status', 'payment_date'];

    protected $casts = [

        'employee_id' => 'integer',
        'commissionable_id' => 'integer',
        'commission_amount' => 'double',
    ];

    public function getAppointment()
    {
        return $this->belongsTo(Appointment::class, 'commissionable_id');
    }
    public function rolewiseCommission($user = null, $commission_status, $startDate = null, $endDate = null)
    {
        if($startDate != null && $endDate != null){
            $query = $this->whereBetween('created_at', [$startDate, $endDate]) // Add date filter
            ->whereIn('commission_status', [$commission_status]);
        }
        $query = $this->whereIn('commission_status', [$commission_status]);
    
        if ($user->user_type == 'vendor') {
            $commissionable_id = $this->where('employee_id', $user->id)
                                      ->where('commission_status', $commission_status)
                                      ->value('commissionable_id');
    
            $query->where('commissionable_id', $commissionable_id)
                  ->whereIn('user_type', ['vendor', 'collector']);
    
            if ($commission_status === 'paid') {
                $query->where('user_type', 'vendor');
            }
    
        } elseif ($user->user_type == 'collector') {
            $query->where('employee_id', $user->id)
                  ->where('user_type', 'collector');
    
        } else {
            $query->whereIn('user_type', ['admin', 'demo_admin']);
        }
    
        return $query->sum('commission_amount');
    }    

   

}
