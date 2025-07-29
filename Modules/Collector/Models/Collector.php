<?php

namespace Modules\Collector\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;


class Collector extends BaseModel
{
  
    use SoftDeletes;

    protected $table = 'collectors';
    protected $fillable = [
        'user_id',
        'education',
        'degree',
        'bio',
        'experience',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Collector\Models\Collector';


    protected $appends = ['feature_image'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && !empty($media) ? $media : '';
    }
   
}
