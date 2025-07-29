<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CashPaymentHistories extends Model
{
    
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [ 'transaction_id', 'appointment_id',
    'action', 'text', 'type', 'sender_id', 'receiver_id', 'datetime', 'status','total_amount','parent_id'
    ];

    protected $casts = [
        'transaction_id'    => 'integer',
        'appointment_id'   => 'integer',
        'sender_id'      => 'integer',
        'parent_id'      => 'integer',
        'total_amount'  => 'double',
        'receiver_id'  => 'integer',
    ];
    
    public function transaction()
    {
        return $this->belongsTo('Modules\Appointment\Models\AppointmentTransaction', 'transaction_id');
    }
    public function appointment()
    {
        return $this->belongsTo('Modules\Appointment\Models\Appointment', 'appointment_id');
    }
    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id');
    }
    public function total_cash_in_hand($user_id)
    {

        $amount = 0;
    
        
        $role = auth()->user()->getRoleNames()->first();
        $payment_history = $this->query();
       
        if (in_array($role, ['collector','vendor'])) {
    
            
            $validActions = $role === 'collector'
                ? ['collector_approved_cash', 'collector_send_vendor','collector_send_admin']
                : ['vendor_approved_cash', 'vendor_send_admin'];
    
                if($validActions == 'collector_send_admin'){
                    $excludeAction = $role === 'collector'
                ? 'admin_approved_cash'
                : '';
                }else{

                    $excludeAction = $role === 'collector'
                    ? 'vendor_approved_cash'
                    : 'admin_approved_cash';
                }
            
            
            $amount = $payment_history->where('receiver_id', $user_id)
                ->whereIn('action', $validActions)
                ->whereNotIn('appointment_id', function ($subQuery) use ($excludeAction, $user_id) {
                    $subQuery->select('appointment_id')
                        ->from('cash_payment_histories')
                        ->where('action', $excludeAction)
                        ->where('sender_id', $user_id);
                })
                ->sum('total_amount'); 
        }
    
        return $amount;
        
    }
}
