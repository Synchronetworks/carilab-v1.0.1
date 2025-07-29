<?php

namespace Modules\Coupon\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Modules\PackageManagement\Models\PackageManagement;
class CouponPackageMapping extends Model
{
    use HasFactory;
    protected $table = 'coupon_package_mapping';
    protected $fillable = ['coupon_id', 'package_id'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    public function package()
    {
        return $this->belongsTo(PackageManagement::class, 'package_id', 'id');
    }
}
