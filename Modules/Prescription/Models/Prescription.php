<?php

namespace Modules\Prescription\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Lab\Models\Lab;
use Spatie\MediaLibrary\HasMedia;
use Modules\Prescription\Models\PrescriptionPackageMapping;
use Modules\Prescription\Models\PrescriptionCatlogMapping;


class Prescription extends BaseModel implements HasMedia
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'prescriptions';
    protected $fillable=['user_id','uploaded_at','note','prescription_status','is_notify','status'];
    const CUSTOM_FIELD_MODEL = 'Modules\Prescription\Models\Prescription';
    protected $casts = [
        'uploaded_at' => 'date',
        'price' => 'double',
    ];
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
  
    public function getPrescriptionUploadUrlAttribute()
    {
        return $this->getFirstMediaUrl('prescription_upload')? setBaseUrlWithFileName($this->getFirstMediaUrl('prescription_upload')) : setBaseUrlWithFileName();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }


    public function prescriptionPackages()
    {
        return $this->hasMany(PrescriptionPackageMapping::class);
    }
   

    public function labMappings()
    {
        return $this->hasMany(PrescriptionLabMapping::class, 'prescription_id');
    }
    public function scopeMyPrescription($query){
        $user = auth()->user();
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed();
        }

        if($user->hasRole('vendor')) {
            return $query->whereHas('labMappings', function($qry) use($user){
                $qry->whereHas('lab', function($q) use($user){
                    $q->where('vendor_id', $user->id);
                });
            });
        }

        if($user->hasRole('user')) {
            return $query->where('user_id', $user->id);
        }

      

        return $query;
    }
}
