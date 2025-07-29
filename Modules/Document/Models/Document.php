<?php

namespace Modules\Document\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'documents';
    protected $fillable=['name', 'slug','status','is_required','user_type'];
    const CUSTOM_FIELD_MODEL = 'Modules\Document\Models\Document';

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
