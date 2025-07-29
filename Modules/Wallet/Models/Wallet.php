<?php

namespace Modules\Wallet\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Wallet extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'wallets';
    protected $fillable = [
        'user_id', 'title', 'amount','status'
    ];
    protected $casts = [
        'user_id'  =>'integer',
        'amount'   => 'double',
        'status'   => 'integer',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Wallet\Models\Wallet';

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
    
    public function users(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
