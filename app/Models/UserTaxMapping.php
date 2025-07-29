<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTaxMapping extends BaseModel
{
    use SoftDeletes;
    protected $table = 'user_tax_mapping'; // Explicitly define table name if it's different
    protected $fillable = [
        'user_id',
        'tax_id',
        'created_by',
        'updated_by'
    ];
    // Define the inverse relationship
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function tax()
    {
        return $this->belongsTo(\Modules\Tax\Models\Tax::class,'tax_id','id');
    }
}
