<?php

namespace Modules\Prescription\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Prescription\Database\factories\PrescriptionLabMappingFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Prescription\Models\Prescription;
use Modules\Lab\Models\Lab;
use Modules\Prescription\Models\PrescriptionPackageMapping;

class PrescriptionLabMapping extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'prescription_lab_mapping';
    protected $fillable=['prescription_id','lab_id','test_id'];
    
 
    public function prescription()
    {
        return $this->belongsTo(Prescription::class,'prescription_id');
    }
    public function lab()
    {
        return $this->belongsTo(Lab::class,'lab_id','id');
    }
    public function testMapping()
    {
        return $this->belongsTo(PrescriptionPackageMapping::class, 'test_id')->with(['package','catalog']);
    }
}
