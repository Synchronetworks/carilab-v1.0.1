<?php

namespace Modules\Bank\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Bank extends BaseModel implements HasMedia
{
   
    use SoftDeletes;

    protected $table = 'banks';
    protected $fillable=['id','ifsc_code','user_id','user_type','bank_name','branch_name','account_no','phone_number','status','created_by','updated_by','deleted_by','is_default'];
    protected $casts = [
        'status' => 'integer',
        'user_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Bank\Models\Bank';

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
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->with('collectorVendormapping');
    }
     // Media handling for logo
     public function getBankImageAttribute()
     {
         return $this->getFirstMediaUrl('bank_attachment') ? setBaseUrlWithFileName($this->getFirstMediaUrl('bank_attachment')) : setBaseUrlWithFileName();
     }

     public function scopeMyBank($query, $usertype = null)
     {
         $user = auth()->user(); 
         $usertype = $usertype ?? 'vendor';
     
         if ($user->hasRole('vendor')) {
             if ($usertype == 'vendor') {
                 return $query->where('user_id', $user->id);
             }
             if($usertype == 'collector'){
                $banks = $query->whereHas('user', function ($q) use ($user) {
                    $q->whereHas('collectorVendormapping', function ($qry) use ($user) {
                        $qry->where('vendor_id', $user->id);
                    });
                });
             }
             
         }
     
         if ($user->hasRole('admin') || $user->hasRole('demo_admin')) {
             return $query->withTrashed(); 
         }
     
         return $query;
     }     
}
