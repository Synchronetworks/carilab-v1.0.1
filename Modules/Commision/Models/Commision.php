<?php

namespace Modules\Commision\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commision extends BaseModel
{
    use SoftDeletes;

    protected $table = 'commisions';
    protected $fillable=['title','type', 'value','user_type','status'];
    const CUSTOM_FIELD_MODEL = 'Modules\Commision\Models\Commision';

    protected $appends = ['feature_image'];

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && ! empty($media) ? $media : '';
    }
  
}
