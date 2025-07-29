<?php

namespace Modules\Banner\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Banner extends BaseModel
{
  
    use SoftDeletes;

    protected $table = 'banners';
    protected $fillable=['name','slug','description','status','banner_type'];
    const CUSTOM_FIELD_MODEL = 'Modules\Banner\Models\Banner';


    protected $appends = ['feature_image'];

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && ! empty($media) ? $media : '';
    }
   
    public function getBannerImageAttribute()
    {
        $media = $this->getFirstMediaUrl('banner_image');
        return $media ?: setBaseUrlWithFileName();
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        
        // Generate unique slug
        $slug = str_replace(' ', '-', strtolower($value));
        $count = static::where('slug', 'LIKE', "{$slug}%")
            ->where('id', '!=', $this->id) // Skip current record when updating
            ->count();
        
        if ($count > 0) {
            $slug = "{$slug}-{$count}";
        }
        
        $this->attributes['slug'] = $slug;
    }
}
