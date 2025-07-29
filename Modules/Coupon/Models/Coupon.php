<?php

namespace Modules\Coupon\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Lab\Models\Lab;
use app\Models\User;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends BaseModel
{

    use SoftDeletes;

    protected $table = 'coupons';
    protected $fillable = [
        'vendor_id',
        'lab_id',
        'coupon_code',
        'discount_type',
        'discount_value',
        'applicability',
        'test_ids',
        'package_ids',
        'start_at',
        'end_at',
        'total_usage_limit',
        'per_customer_usage_limit',
        'status',
    ];

    protected $casts = [
        'applicability' => 'array',
        'test_ids' => 'array',
        'package_ids' => 'array',
        'status' => 'boolean',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Coupon\Models\Coupon';

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

    public function packages()
    {
        return $this->hasMany(CouponPackageMapping::class, 'coupon_id', 'id');
    }

    public function couponlists()
    {
      return $this->hasMany(AppointmentTransaction::class, 'coupon_id', 'id')->with('appointment');
    }

    public function tests()
    {
        return $this->hasMany(CouponTestMapping::class, 'coupon_id', 'id');
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
    public function scopeMyCoupon(Builder $query)
    {
        $user = auth()->user(); 

        if ($user->hasRole('vendor')) {
            return $query->where('vendor_id', $user->id);
        }

        if ($user->hasRole('admin') || $user->hasRole('demo_admin') ) {
            return $query->withTrashed(); 
        }

        return $query;
    }
}
