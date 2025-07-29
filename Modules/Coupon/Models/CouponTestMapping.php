<?php

namespace Modules\Coupon\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;
class CouponTestMapping extends Model
{
 
    protected $table = 'coupon_test_mapping';
    protected $fillable = ['coupon_id', 'test_id'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    public function test()
    {
        return $this->belongsTo(CatlogManagement::class, 'test_id', 'id');
    }
}
