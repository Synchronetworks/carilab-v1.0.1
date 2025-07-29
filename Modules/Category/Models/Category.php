<?php

namespace Modules\Category\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Builder;

class Category extends BaseModel
{

    use SoftDeletes;

    protected $table = 'categories';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'file_url'
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Category\Models\Category';

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
    public function getCategoryImageAttribute()
    {
        $media = $this->getFirstMediaUrl('category_image');
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

    public function scopeMyCategory(Builder $query)
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('vendor')) {    
                return $query;
        }
        
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed();
        }

        return $query;
    }
}
