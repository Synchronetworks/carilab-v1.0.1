<?php

namespace Modules\PackageManagement\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PackageManagement\Models\PackageCatlogMapping;
use Modules\Lab\Models\Lab;
use App\Models\User;
use Spatie\MediaLibrary\HasMedia;
class PackageManagement extends BaseModel implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'packagemanagements';
    protected $fillable=['slug','vendor_id','lab_id','name','description','price','start_at','end_at','is_discount','discount_type','discount_price','status','is_featured','is_home_collection_available','parent_id','created_by','updated_by','deleted_by'];
    const CUSTOM_FIELD_MODEL = 'Modules\PackageManagement\Models\PackageManagement';
    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'discount_price' => 'double',
        'price' => 'double',
    ];
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    
        // Only generate slug if it's not already set (i.e., during creation)
        if (!isset($this->attributes['slug']) || empty($this->attributes['slug'])) {
            $baseSlug = str_replace(' ', '-', strtolower(trim($value))); 
            $slug = $baseSlug;
            $count = 1;
    
            // Ensure uniqueness of the slug
            while (self::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
    
            $this->attributes['slug'] = $slug;
        }
    }
    
    public function packageCatlogMapping()
    {
        return $this->hasMany(PackageCatlogMapping::class,'package_id')->with('catalog');
    }
    public function lab()
    {
        return $this->belongsTo(Lab::class,'lab_id');
    }
    public function vendor()
    {
        return $this->belongsTo(User::class,'vendor_id');
    }
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
    public function getPackageImageAttribute()
    {
        return $this->getFirstMediaUrl('package_image') ?? setBaseUrlWithFileName();
    }
   
    public function getFinalDiscountPriceAttribute()
    {
        $originalPrice = $this->price; // Assuming 'price' is the original price attribute
        $discountType = $this->discount_type; // Assuming 'discount_type' is either 'percentage' or 'fixed'
        $discountValue = $this->discount_price; // Assuming 'discount_price' is the discount value

        if ($discountType === 'percentage') {
            return $originalPrice - ($originalPrice * ($discountValue / 100));
        } elseif ($discountType === 'fixed') {
            return $originalPrice - $discountValue;
        }

        return $originalPrice; // No discount applied
    }

    public function scopeMyPackageManagement($query){
        $user = auth()->user();
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed();
        }

        if($user->hasRole('vendor')) {
            return $query->where('vendor_id', $user->id);
        }

        return $query;
    }

    public function appointments()
    {
        return $this->hasMany('Modules\Appointment\Models\Appointment', 'test_id', 'id');
    }

    
}
