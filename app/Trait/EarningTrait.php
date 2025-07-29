<?php
namespace App\Trait;

use Modules\Appointment\Models\Appointment;
use Modules\Commision\Models\CommissionEarning;
use Modules\Payout\Models\Payout;

trait EarningTrait
{
    /**
     * Scope a query to filter users with required relationships.
     */
    public function scopeWithCommissionData($query,$userType)
    {
        return $query->select('users.*')
            ->with(['commission_earning', 'userCommissionMapping', 'collector', 'lab'])
            ->whereHas('commission_earning', function ($q) use ($userType){
                $q->whereIn('commission_status', ['unpaid', 'paid'])
                    ->where('user_type', $userType)
                    ->where('commissionable_type', 'Modules\Appointment\Models\Appointment')
                    ->whereNull('deleted_at');
            });
    }

    /**
     * Get the total commission amount for a user.
     */
    public function getTotalCommissionAmountAttribute()
    {
        return $this->commission_earning()
            ->whereNull('deleted_at')
            ->where('commission_status', 'unpaid')
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->sum('commission_amount') ?? 0;
    }

    /**
     * Get the total number of completed appointments for a user.
     */
    public function getTotalAppointmentsAttribute()
    {
        return $this->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereIn('commission_status', ['unpaid', 'paid'])
            ->distinct('commissionable_id')
            ->count();
    }

    /**
     * Get total service amount.
     */
    public function getTotalServiceAmountAttribute()
    {
        return $this->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereIn('commission_status', ['unpaid', 'paid'])
            ->get()
            ->sum(fn ($commission) => Appointment::where('id', $commission->commissionable_id)->value('test_discount_amount') ?? 0);
    }

    /**
     * Get total Tax amount.
     */
    public function getTotalTaxAmountAttribute()
    {
        return $this->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereIn('commission_status', ['unpaid', 'paid'])
            ->get()
            ->sum(function ($commission) {
                return Appointment::where('id', $commission->commissionable_id)
                    ->whereHas('transactions')
                    ->first()?->transactions()->sum('total_tax_amount') ?? 0;
            });
    }

    /**
     * Get total admin earnings.
     */
    public function getTotalAdminEarningsAttribute()
    {
        return $this->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereIn('commission_status', ['unpaid', 'paid'])
            ->get()
            ->sum(fn ($commission) => CommissionEarning::where('commissionable_id', $commission->commissionable_id)
                ->where('user_type', 'admin')
                ->whereIn('commission_status', ['unpaid', 'paid'])
                ->value('commission_amount') ?? 0);
    }

    /**
     * Get total vendor earnings.
     */
    public function getTotalVendorEarningsAttribute()
    {
        return $this->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereIn('commission_status', ['unpaid', 'paid'])
            ->get()
            ->sum(fn ($commission) => CommissionEarning::where('commissionable_id', $commission->commissionable_id)
                ->where('user_type', 'vendor')
                ->whereIn('commission_status', ['paid', 'unpaid'])
                ->value('commission_amount') ?? 0);
    }
/**
     * Get total collector earnings.
     */
    public function getTotalCollectorEarningsAttribute()
    {
        return $this->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereIn('commission_status', ['unpaid', 'paid'])
            ->get()
            ->sum(fn ($commission) => CommissionEarning::where('commissionable_id', $commission->commissionable_id)
                ->where('user_type', 'collector')
                ->whereIn('commission_status', ['paid', 'unpaid'])
                ->value('commission_amount') ?? 0);
    }
    /**
     * Get total collector paid earnings.
     */
    public function getCollectorPaidEarningsAttribute()
    {
        return Payout::where('user_id', $this->id)
            ->where('user_type', 'collector')
            ->sum('amount') ?? 0;
    }
    public function getVendorPaidEarningsAttribute()
    {
        return Payout::where('user_id', $this->id)
            ->where('user_type', 'vendor')
            ->sum('amount') ?? 0;
    }
}
