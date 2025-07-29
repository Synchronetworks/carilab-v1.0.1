<?php

namespace Modules\Collector\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollectorVendorMapping extends BaseModel
{
    use SoftDeletes;
    protected $table = 'collector_vendor_mapping'; 
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['collector_id','vendor_id','status','created_by','updated_by','deleted_by'];
    
 
    public function collector()
    {
        return $this->belongsTo(User::class,'collector_id','id');
    }
    public function vendor()
    {
        return $this->belongsTo(User::class,'vendor_id','id');
    }
   
}
