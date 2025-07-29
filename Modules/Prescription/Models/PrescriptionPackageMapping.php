<?php

namespace Modules\Prescription\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Prescription\Models\Prescription;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\CatlogManagement\Models\CatlogManagement;

class PrescriptionPackageMapping extends BaseModel implements HasMedia
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'prescription_test_mapping';
    protected $fillable=['prescription_id','test_id','price','start_at','end_at','is_discount','discount_type','discount_price','type'];
    const CUSTOM_FIELD_MODEL = 'Modules\Prescription\Models\Prescription';
    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'discount_price' => 'double',
        'price' => 'double',
    ];
    
  
    public function prescription()
    {
        return $this->belongsTo(Prescription::class,'prescription_id');
    }
    // Relationship for PackageManagement (if the test is a package)
    public function package()
    {
        return $this->belongsTo(PackageManagement::class, 'test_id');
    }

    // Relationship for CatlogManagement (if the test is a catalog)
    public function catalog()
    {
        return $this->belongsTo(CatlogManagement::class, 'test_id');
    }

    // Accessor to determine if this is a package or catalog
    public function getTestAttribute()
    {
        // If the type is 'package', return the package relationship
        if ($this->type === 'package') {
            return $this->package;
        }

        // Otherwise, return the catalog relationship
        return $this->catalog;
    }
}
