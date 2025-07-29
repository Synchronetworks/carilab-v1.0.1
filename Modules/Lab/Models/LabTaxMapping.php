<?php

namespace Modules\Lab\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Lab\Database\factories\LabTaxMappingFactory;

class LabTaxMapping extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'lab_tax_mapping';
    protected $fillable = [
        'lab_id',
        'tax_id',
    ];
    /**
     * The attributes that are mass assignable.
     */
    
    protected static function newFactory(): LabTaxMappingFactory
    {
        //return LabTaxMappingFactory::new();
    }
    public function lab()
    {
        return $this->belongsTo(\Modules\Lab\Models\Lab::class,'lab_id','id');
    }
    public function tax()
    {
        return $this->belongsTo(\Modules\Tax\Models\Tax::class,'tax_id','id');
    }
}
