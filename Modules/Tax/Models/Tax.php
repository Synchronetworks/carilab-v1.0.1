<?php

namespace Modules\Tax\Models;

use App\Models\BaseModel;
use App\Models\UserTaxMapping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tax extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'taxes';

    protected $fillable=['title','type', 'value', 'status'];
    const CUSTOM_FIELD_MODEL = 'Modules\Tax\Models\Tax';

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
    protected static function newFactory()
    {
        return \Modules\Tax\database\factories\TaxFactory::new();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    public function user()
    {
        return $this->belongsTo(UserTaxMapping::class, 'id', 'tax_id');
    }
}
