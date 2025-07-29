<?php

namespace Modules\CatlogManagement\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PackageManagement\Models\PackageCatlogMapping;
use Modules\Lab\Models\Lab;
use App\Models\User;
use Modules\Category\Models\Category;
use Modules\Prescription\Models\PrescriptionCatlogMapping;
class CatlogManagement extends BaseModel
{
  
    use SoftDeletes;

    protected $table = 'catlogmanagements';
    protected $fillable = [
        'slug',
        'name',
        'code',
        'type',
        'equipment',
        'description',
        'category_id',
        'vendor_id',
        'lab_id',
        'price',
        'duration',
        'test_report_time',
        'instructions',
        'status',
        'is_home_collection_available',
        'additional_notes',
        'restrictions',
        'is_featured',
        'parent_id',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\CatlogManagement\Models\CatlogManagement';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */


    protected $appends = ['feature_image'];

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

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && ! empty($media) ? $media : '';
    }
   

    protected $casts = [
        'equipment' => 'array',
        'type' => 'array',
        'status' => 'integer',
        'is_home_collection_available' => 'integer',
        'is_featured' => 'integer',
        'price' => 'double',
    ];

    public function getTestImageAttribute()
    {
        return $this->getFirstMediaUrl('test_image') ?? setBaseUrlWithFileName();
    }

    public function getGuidelinesPdfAttribute()
    {
        return $this->getFirstMediaUrl('guidelines_pdf') ?? setBaseUrlWithFileName();
    }
    public function packageCatlogMapping()
    {
        return $this->hasMany(PackageCatlogMapping::class,'catalog_id')->with('package');
    }
    public function lab()
    {
        return $this->belongsTo(Lab::class,'lab_id');
    }
    public function vendor()
    {
        return $this->belongsTo(User::class,'vendor_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function prescriptioncatlog()
    {
        return $this->hasMany(PrescriptionCatlogMapping::class,'catlog_id','id');
    }
    public function appointments()
    {
        return $this->hasMany('Modules\Appointment\Models\Appointment', 'test_id', 'id');
    }
    public function scopeMyCatlogManagement($query){
        $user = auth()->user();
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed();
        }

        if($user->hasRole('vendor')) {
            return $query->where('vendor_id', $user->id);
        }

        return $query;
    }
}
