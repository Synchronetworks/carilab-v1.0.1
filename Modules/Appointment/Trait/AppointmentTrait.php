<?php

namespace Modules\Appointment\Trait;
use Modules\Appointment\Models\Appointment;
use App\Models\User;
use Modules\Lab\Models\Lab;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Lab\Trait\HasTaxList;
use Modules\Coupon\Models\Coupon;
use Modules\Commision\Models\Commision;
use Modules\Commision\Models\CommissionEarning;
trait AppointmentTrait
{
    use HasTaxList;
    public function getTestAmount($test_id, $test_type = 'test_case',$lab_id,$coupon_id = null)
    {
        if($test_type == 'test_case'){
            $data = CatlogManagement::where('id', $test_id)->first();
        }else{
            $data = PackageManagement::where('id', $test_id)->first();
        }

        $data['test_type'] = $test_type;
        $data['test_id'] = $test_id;
        if (!$data) {

            return ['amount' => 0, 'test_discount_amount' => 0, 'total_amount' => 0,'duration' => 0];
        }

        $amount = $data->price ?? 0;
        $discount_amount = 0;

        if ($data->is_discount == 1) {

            $discount_amount = ($data->discount_type == 'percentage')
                ? $amount * $data->discount_price / 100
                : $data->discount_price;
            
            $amount = $amount - $discount_amount;
        }
       
        if ($coupon_id != null) {
           
            $coupon_data = $this->applyCoupon($coupon_id,$data,$amount);
           
            $data['coupon_amount'] = $coupon_data['coupon_amount'] ?? 0;
            $data['coupon'] = $coupon_data['coupon_list'] ?? [];
            $data['coupon_id'] = $coupon_data['coupon_id'] ?? null;
            $amount = $amount - $data['coupon_amount'];
        }
        $tax_amount = 0;
        $tax_data = $amount > 0 ? $this->TaxCalculation($amount,$lab_id) : 0;
        $tax_amount = $tax_data['tax_amount'] ?? 0;
        $tax_list = $tax_data['tax_list'] ?? [];

        $total_amount = $amount + $tax_amount;
       

        return [
            'amount' => $data->price ?? 0,
            'test_discount_amount' => $amount,
            'total_amount' => $total_amount,
            'duration' => $data->duration ?? 0,
            'discount_type' => $data->discount_type ?? '',
            'discount_value' => $data->discount_price ?? 0,
            'discount_amount' => $discount_amount,
            'tax_amount' => $tax_amount,
            'tax_list' => $tax_list,
            'coupon_amount' => $data['coupon_amount'],
            'coupon' => $data['coupon'],
            'coupon_id' => $data['coupon_id'],
        ];
    }

    function TaxCalculation($amount,$lab_id)
    {
        $lab = Lab::find($lab_id);
        $tax_list = $lab->getTaxListAttribute();

        $totalTaxAmount = 0;

        foreach ($tax_list as $tax) {
            if (!isset($tax['type'], $tax['value'])) {
                continue; // Skip invalid tax entries
            }
        
            $taxType = strtolower($tax['type']); // Convert to lowercase for consistency
        
            if ($taxType == 'percentage') {
                $totalTaxAmount += ($tax['value'] / 100) * $amount;
            } elseif ($taxType == 'fixed') {
                $totalTaxAmount += $tax['value'];
            }
        }

        return [
            'tax_amount' => $totalTaxAmount,
            'tax_list' => $tax_list,
        ];
    }

   

        public function applyCoupon($couponCode, $data, $amount = 0)
        {
            $testType = $data['test_type'];
            $testId = $data['test_id'];
            $labId = $data['lab_id'] ?? null;

            $coupon = Coupon::where('coupon_code', $couponCode)
                ->where('end_at', '>', now())
                ->where('status', 1)
                ->where(function ($query) use ($testType, $testId) {
                    $query->whereJsonContains('applicability', 'all');

                    if ($testType == 'test_case') {
                        $query->orWhereHas('tests', function ($q) use ($testId) {
                            $q->where('test_id', $testId);
                        })->whereJsonContains('applicability', 'specific_tests');
                    }

                    if ($testType == 'test_package') {
                        $query->orWhereHas('packages', function ($q) use ($testId) {
                            $q->where('package_id', $testId);
                        })->whereJsonContains('applicability', 'specific_packages');
                    }
                });
            // Filter by lab_id if provided
            if (!empty($labId)) {
                $coupon->where('lab_id', $labId);
            }

            $coupon = $coupon->first();

            if (!$coupon) {
                return [
                    'coupon_id' => null,
                    'coupon_amount' => 0,
                    'coupon_list' => null,
                ];
            }

            // Calculate discount amount
            $couponAmount = $this->calculateDiscountAmount($coupon, $amount);

            return [
                'coupon_id' => $coupon->id,
                'coupon_amount' => $couponAmount,
                'coupon_list' => $coupon,
            ];
        }

    private function calculateDiscountAmount($coupon,$amount)
    {
        return $coupon->discount_type == 'percentage' 
            ? ($amount * $coupon->discount_value) / 100 
            : $coupon->discount_value;
    }
    public function commissionData($data,$vendorcommissionAmount = 0)
    {
        
        $appointment = Appointment::where('id', $data['id'])
            ->with('transactions','appointmentCollectorMapping.collector')
            ->first();
        $collectorid = $appointment->appointmentCollectorMapping->collector_id ? $appointment->appointmentCollectorMapping->collector_id : null;
        $user = User::where('id',$collectorid)->first();    
        if (!$appointment || !$appointment->appointmentCollectorMapping->collector || $user->user_type !== 'collector') {
            $commission_data['commission_amount'] = 0;
    
            return [
                'commission_data' => $commission_data,
            ];
        }
        
        $collector = $appointment->appointmentCollectorMapping->collector ?? null;

        
        $without_tax_amount = $appointment->test_discount_amount ?? 0;
        if($vendorcommissionAmount > 0){
            $without_tax_amount = $vendorcommissionAmount;
        }
        $commission_amount = $this->calculateCommission($collector, $without_tax_amount);
        
        $collector_commission_list = $collector->userCommissionMapping()->get();

        $commissionStatus = $appointment->transactions->payment_status == 'paid' ? 'unpaid' : 'pending';

        $commission_data = [
            'employee_id' => $appointment->appointmentCollectorMapping->collector_id,
            'user_type' => 'collector',
            'commission_amount' => $commission_amount,
            'commission_status' => $commissionStatus,
            'commissions' => $collector_commission_list->isNotEmpty() ? $collector_commission_list->toJson() : null,
            'payment_date' => null,
        ];

        return [
            'commission_data' => $commission_data,
        ];
    }

    public function vendorCommissionData($data,$collectorcommissionAmount = 0)
    {
        $appointment = Appointment::where('id', $data['id'])
            ->with('transactions')
            ->first();

        if (!$appointment || !$appointment->vendor) {
            return null;
        }
        
        $vendor = $appointment->vendor ?? null;
        if($vendor == null){
            return null;
        }
        
        $without_tax_amount = $appointment->test_discount_amount  ?? 0;

        $commission_percentage = $this->calculateCommission($vendor, $without_tax_amount);
        $commission_amount = $commission_percentage;
        if($collectorcommissionAmount > 0){
            $commission_amount = $commission_percentage - $collectorcommissionAmount;
        }
        

        $vendor_commission_list = $vendor->userCommissionMapping()->get();


        $commissionStatus = $appointment->transactions->payment_status == 'paid' ? 'unpaid' : 'pending';

        $commission_data = [
            'employee_id' => $appointment->vendor_id,
            'user_type' => 'vendor',
            'commission_amount' => $commission_amount,
            'commission_status' => $commissionStatus,
            'commissions' => $vendor_commission_list->isNotEmpty() ? $vendor_commission_list->toJson() : null,
            'payment_date' => null,
        ];

        return [
            'commission_data' => $commission_data,
        ];
    }

    private function calculateCommission($employee, $total_test_amount)
    {
        $commission_amount = 0; 
        if (isset($employee->userCommissionMapping) && $employee->userCommissionMapping->isNotEmpty()) {
            foreach ($employee->userCommissionMapping as $commission) {
                if ($commission) {
                    $commission_value = $commission->commission;
                    $commission_type = $commission->commission_type;

                    if ($commission_type == 'Percentage') {
                        $commission_amount += $commission_value * $total_test_amount / 100;
                    } else {
                        $commission_amount += $commission_value;
                    }
                }
            }
        }else{
            $commissions = Commision::where('user_type',$employee->user_type)->get();
            foreach ($commissions as $commission) {
                if ($commission) {
                    $commission_value = $commission->value;
                    $commission_type = $commission->type;

                    if ($commission_type == 'Percentage') {
                        $commission_amount += $commission_value * $total_test_amount / 100;
                    } else {
                        $commission_amount += $commission_value;
                    }
                }
            }
        }
      
        return  $commission_amount;
    }

    public function commissionDistribute($vendor = null, $appointment = null, $transactions = null)
    {
        $earningData = $this->commissionData($appointment,0);
        $collectorid = $appointment->appointmentCollectorMapping->collector_id ? $appointment->appointmentCollectorMapping->collector_id : null;
        $user = User::where('id',$collectorid)->first();
        $isMultiVendor = multiVendor() == 1;
        $adminId = User::where('user_type', 'admin')->value('id');
        
        if (!$isMultiVendor) {
            if (!empty($collectorid) && $user->user_type == 'collector' && $earningData) {
                $this->saveCommission($appointment, $earningData['commission_data']);
            }
            $adminEarningData = $this->createAdminEarningData($vendor, $adminId, $transactions, $appointment->test_discount_amount, $earningData['commission_data']['commission_amount']);
            $this->saveCommission($appointment, $adminEarningData);
            return;
        }
        
        if ($vendor && $vendor->user_type == 'vendor') {
            $vendorEarning = $this->vendorCommissionData($appointment,0);
           
            if ($appointment->vendor_id && $vendorEarning) {
        
                $this->saveCommission($appointment, $vendorEarning['commission_data']);
                $earningData = $this->commissionData($appointment,$vendorEarning['commission_data']['commission_amount']);
                if (!empty($collectorid) && $user->user_type == 'collector' ) {
                    $this->saveCommission($appointment, $earningData['commission_data']);
                }
                $vendorEarning = $this->vendorCommissionData($appointment,$earningData['commission_data']['commission_amount']);
                $this->saveCommission($appointment, $vendorEarning['commission_data']);
                $adminEarningData = $this->createAdminEarningData('admin', $adminId, $transactions, $appointment->test_discount_amount,$earningData['commission_data']['commission_amount'], $vendorEarning['commission_data']['commission_amount']);
                $this->saveCommission($appointment, $adminEarningData);
            }

        }else {
            if (!empty($collectorid) && $user->user_type == 'collector' && $earningData) {
                $this->saveCommission($appointment, $earningData['commission_data']);
            }
            $adminEarningData = $this->createAdminEarningData($vendor, $adminId, $transactions, $appointment->test_discount_amount, $earningData['commission_data']['commission_amount']);
            $this->saveCommission($appointment, $adminEarningData);
           
        }
    }

    private function saveCommission($appointment, array $data)
    {
        $commission = $appointment->commission()->where([
            'commissionable_id' => $appointment->id,
            'commissionable_type' => get_class($appointment),
            'employee_id' => $data['employee_id']
        ])->first();
    
        if ($commission) {
            $commission->update($data); // Update existing record
        } else {
            $appointment->commission()->save(new CommissionEarning($data));
        }
        
    }

    private function createAdminEarningData($vendor = null, $adminId = 1, $transactions = null, $testDiscountAmount = 0,$collectorcommissionAmount = 0, $vendorcommissionAmount = 0)
    {
        return [
            'user_type' => $vendor->user_type ?? 'admin',
            'employee_id' => $vendor->id ?? $adminId,
            'commissions' => null,
            'commission_status' => $transactions->payment_status == 'paid' ? 'paid' : 'pending',
            'commission_amount' => $testDiscountAmount - $collectorcommissionAmount - $vendorcommissionAmount,
        ];
    }

    private function createCollectorEarningData($appointment, $collector, $commissionAmount, $collectorCommissionList, $transactions)
    {
        return [
            'employee_id' => $appointment->appointmentCollectorMapping->collector_id ?? null,
            'user_type' => 'collector',
            'commission_amount' => $commissionAmount,
            'commission_status' => $transactions->payment_status === 'paid' ? 'unpaid' : 'pending',
            'commissions' => $collectorCommissionList->isNotEmpty() ? $collectorCommissionList->toJson() : null,
            'payment_date' => null,
        ];
    }

    public function getTimeZone()
    {
        $timezone = \App\Models\Setting::first();

        return $timezone->default_time_zone ?? 'UTC';
    }



}
