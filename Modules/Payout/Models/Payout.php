<?php

namespace Modules\Payout\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Expr\Cast;
use App\Models\User;
class Payout extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'payouts';
    protected $fillable=['user_id','user_type','payment_method','description','amount','paid_date'];
    protected $cast = [
            'amount' =>'double',
            'paid_date' => 'datetime',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Payout\Models\Payout';

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
   
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id'  );
    }
  
}
