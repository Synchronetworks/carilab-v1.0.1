<?php

namespace Modules\Appointment\Models;

use App\Models\BaseModel;

use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Appointment\Models\Appointment;
use Illuminate\Database\Eloquent\SoftDeletes;


class AppointmentPackageMapping extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'appointment_package_mapping';
    protected $fillable = ['appointment_id','test_id', 'package_id','amount','status'];
    protected $casts = [
        'amount' => 'double',
    ];
  
    public function catlog()
    {
        return $this->belongsTo(CatlogManagement::class, 'test_id');
    }
    public function package()
    {
        return $this->belongsTo(PackageManagement::class, 'package_id');
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
