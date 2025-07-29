<?php

namespace Modules\Collector\Models;

use App\Models\BaseModel;
use App\Models\User;
use Modules\Lab\Models\Lab;
use Illuminate\Database\Eloquent\SoftDeletes;
class CollectorLabMapping extends BaseModel
{
    use SoftDeletes;
    protected $table = 'collector_lab_mapping'; 
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['collector_id','lab_id','status','created_by','updated_by','deleted_by'];
    
  

    public function collector()
    {
        return $this->belongsTo(User::class,'collector_id','id');
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class,'lab_id','id');
    }
}

