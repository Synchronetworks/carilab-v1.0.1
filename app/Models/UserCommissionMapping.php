<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Commision\Models\Commision;

class UserCommissionMapping extends BaseModel
{
    use SoftDeletes;
    protected $table = 'user_commission_mapping'; // Explicitly define table name if it's different
    protected $fillable = [
        'user_id',
        'commission_id',
        'commission_type',
        'commission',
        'created_by'
    ];
    // Define the inverse relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commissions(){
        return $this->belongsTo(Commision::class,'commission_id','id');
    }
}
