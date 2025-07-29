<?php

namespace Modules\Review\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Lab\Models\Lab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Review extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reviews';
    protected $fillable=['status','user_id','collector_id','lab_id','rating','review','created_by','updated_by','deleted_by'];
    const CUSTOM_FIELD_MODEL = 'Modules\Review\Models\Review';

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
   

    public function user(){
     
            return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
        
    }

    public function collector(){
        return $this->belongsTo(User::class, 'collector_id', 'id')->withTrashed();
    }
    public function lab(){
        return $this->belongsTo(Lab::class, 'lab_id', 'id')->withTrashed();
    }

    public function scopeVisibleToUser(Builder $query, $user = null)
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return $query;
        }

        if ($user->hasRole('vendor')) {
            // Ensure vendor can only see relevant reviews
            $query->whereHas('collector', function ($query) use ($user) {
                $query->where('user_type', 'collector')
                      ->whereHas('collectorVendormapping', function ($query) use ($user) {
                          $query->where('vendor_id', $user->id);
                      });
            })->orWhereHas('lab', function ($query) use ($user) {
                $query->where('vendor_id', $user->id);
            });
        }

        return $query;
    }   
}
