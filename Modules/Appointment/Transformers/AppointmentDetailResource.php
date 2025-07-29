<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use  Modules\Appointment\Transformers\AppointmentCollectorMappingResource;
use Modules\CatlogManagement\Transformers\CatlogResource;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Review\Models\Review;
use App\Models\Setting;

class AppointmentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $noOfDecimal = Setting::getSettings('digitafter_decimal_point') ?? 2;
        $testCaseList = [];
        if ($this->test_type === 'test_package' && $this->package) {
            $catalogIds = $this->package->packageCatlogMapping
                ? $this->package->packageCatlogMapping->pluck('catalog_id')->toArray()
                : [];
            $testCaseList = CatlogResource::collection(CatlogManagement::whereIn('id', $catalogIds)->get());
        }

        $reviewCount = Review::where('lab_id', ($this->lab)->id)->count();
        $reviewSum = Review::where('lab_id', ($this->lab)->id)->sum('rating');
        
        $averageRating = $reviewCount > 0 ? $reviewSum / $reviewCount : 0;

        return [
            'id' => $this->id,
            'address_id' => $this->address_id,
            'collection_type' => $this->collection_type,
            'amount' => round($this->amount ?? 0, $noOfDecimal),
            'sub_total' => round($this->test_discount_amount ?? 0, $noOfDecimal),
            'paid_amount' => round($this->total_amount ?? 0, $noOfDecimal),
            'status' => $this->status,
            'appointment_date' => Setting::formatDate($this->appointment_date),
            'appointment_time' => Setting::formatTime($this->appointment_time),
            'user_medical_report' => $this->getMedicalReportAttribute(),
            'symptoms' => $this->symptoms ?? '',
            'submission_status' => $this->submission_status ?? 'pending',
            'test_type' => $this->test_type,
            // Test Case Information
            'test_case_info' => $this->test_type === 'test_case' ? [
                'test_case_name' => optional($this->catlog)->name,
                'test_case_image' => optional($this->catlog)->getTestImageAttribute(),
            ] : null,

            // Lab Information
            'lab_info' => [
                'lab_id' => optional($this->lab)->id,
                'lab_name' => optional($this->lab)->name,
                'lab_logo' => optional($this->lab)->getLogoUrlAttribute(),
                'lab_phone' => optional($this->lab)->phone_number,
                'lab_email' => optional($this->lab)->email,
                'address' => optional($this->lab)->address_line_1. ',' .optional($this->lab)->address_line_2 ,
                'payment_method_list' => optional($this->lab)->payment_modes,
                'payment_gateway' => optional($this->lab)->payment_gateways,
                'certified_by' => optional($this->lab)->accreditation_type,
                'rating' => $averageRating,
                'available_time_slots' => optional($this->lab)->getLabSession(optional($this->lab)->labSessions),
            ],

            // Vendor Information
            'vendor_info' => [
                'id' => optional($this->vendor)->id,
                'full_name' => trim(optional($this->vendor)->first_name . ' ' . optional($this->vendor)->last_name),
                'profile_image' => setBaseUrlWithFileName(optional($this->vendor)->profile_image),
                'mobile' => optional($this->vendor)->mobile,
                'email' => optional($this->vendor)->email,
                'user_type' => optional($this->vendor)->user_type,
                'is_verified_user' =>  optional($this->vendor)->is_verify,
            ],

            // Customer Information
            'customer_info' => [
                'id' => optional($this->customer)->id,
                'full_name' => trim(optional($this->customer)->first_name . ' ' . optional($this->customer)->last_name),
                'profile_image' => setBaseUrlWithFileName(optional($this->customer)->profile_image),
                'address' => optional($this->customer)->address,
                'mobile' => optional($this->customer)->mobile,
            ],

            // Package Information (Only for 'test_package' type)
            'package_info' => $this->test_type === 'test_package' ? [
                'package_id' => optional($this->package)->id,
                'package_name' => optional($this->package)->name,
                'original_price' => optional($this->package)->price,
                'description' => optional($this->package)->description,
                'status' => optional($this->package)->status,
                'package_image' => optional($this->package)->getPackageImageAttribute(),
                'test_case_list' => $testCaseList,
                'is_discount' => optional($this->package)->is_discount,
                'discount_type' => optional($this->package)->discount_type,
                'discount_value' => optional($this->package)->discount_price,
                'discount_price' => optional($this->package)->getFinalDiscountPriceAttribute(),
            ] : null,

            // Collector, Payment, and Reviews
            'collector_info' => new AppointmentCollectorMappingResource($this->appointmentCollectorMapping),
            'payment' => $this->transactions,
            'lab_review' => $this->lab_review,
            'collector_review' => $this->collector_review,

            // Other Member Information
            'other_member_info' => $this->other_member_id ? [
                'id' => optional($this->othermember)->id,
                'full_name' => trim(optional($this->othermember)->first_name . ' ' . optional($this->othermember)->last_name),
                'profile_image' => setBaseUrlWithFileName(optional($this->othermember)->profile_image),
                'relation' => optional($this->othermember)->relation,
            ] : null,

            // Payment History (if cash payment)
            'payment_history' => $this->transactions && $this->transactions->payment_type === 'cash' ? [
                'status' => $this->transactions->payment_status,
                'message' => optional($this->cashhistory->sortByDesc('id')->first())->text,
                'date_time' => optional($this->cashhistory->sortByDesc('id')->first())->datetime,
            ] : null,
            
            'generated_reports'  => getAttachmentArray($this->getMedia('report_generate')),
            'reschedule_reason' => $this->reschedule_reason ? $this->reschedule_reason : null,
            'cancellation_reason' => $this->cancellation_reason ? $this->cancellation_reason : null,
        ];
    }

}
