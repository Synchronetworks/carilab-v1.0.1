<?php

namespace Modules\Vendor\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vendors';
    protected $fillable = [
       'user_id','commission_type', 'commission', 'country', 'state', 'city',
        'address', 'set_as_featured', 'tax_id',
    ];

    protected $hidden = ['password'];
    const CUSTOM_FIELD_MODEL = 'Modules\Vendor\Models\Vendor';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */


    protected $appends = ['profile_image'];

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && ! empty($media) ? $media : '';
    }
   

    protected $casts = [
        'tax_id' => 'array',
    ];
}
