<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddressMapping extends BaseModel
{
    use SoftDeletes;
    protected $table = 'user_address_mapping';
    protected $fillable = ['user_id', 'type', 'address','latitude','longitude','created_by','updated_by','deleted_by'];
    protected $casts = [
        'latitude'=> 'double',
        'longitude'=> 'double',
        'user_id'    => 'integer',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
