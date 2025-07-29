<?php

namespace Modules\Lab\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Lab\Database\factories\LabLocationMappingFactory;

class LabLocationMapping extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'lab_location_mapping';
    protected $fillable = [
        'lab_id',
        'location_id',
    ];
    /**
     * The attributes that are mass assignable.
     */
    
    protected static function newFactory(): LabLocationMappingFactory
    {
        //return LabLocationMappingFactory::new();
    }
    public function lab()
    {
        return $this->hasMany(\Modules\Lab\Models\Lab::class);
    }
}
