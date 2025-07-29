<?php

namespace Modules\Lab\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Lab\Database\factories\LabSessionFactory;
use App\Models\BaseModel;
use Modules\Lab\Models\Lab;
use Illuminate\Database\Eloquent\Builder;
class LabSession extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['lab_id','day','start_time','end_time','is_holiday','breaks','created_by','updated_by','deleted_by'];

    protected $table = 'lab_session';
    protected $casts = [
        'breaks' => 'array',
    ];
    protected static function newFactory(): LabSessionFactory
    {
        //return LabSessionFactory::new();
    }
    public function lab(){
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function scopeMyLabsession(Builder $query)
    {
        $user = auth()->user(); 

        if ($user->hasRole('vendor')) {
            // Ensure vendors only see lab sessions for their own labs
            return $query->whereHas('lab', function ($q) use ($user) {
                $q->where('vendor_id', $user->id);
            });
        }

        if ($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed(); 
        }

        return $query;
    }

    
}
