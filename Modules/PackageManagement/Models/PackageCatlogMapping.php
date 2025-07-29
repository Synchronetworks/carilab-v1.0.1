<?php

namespace Modules\PackageManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PackageManagement\Database\factories\PackageCatlogMappingFactory;
use App\Models\BaseModel;
use Modules\CatlogManagement\Models\CatlogManagement;
class PackageCatlogMapping extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'package_catlog_mapping';
    protected $fillable = ['package_id','catalog_id','created_by','updated_by','deleted_by'];
    /**
     * The attributes that are mass assignable.
     */
    public function package()
    {
        return $this->belongsTo(PackageManagement::class,'package_id');
    }
    public function catalog()
    {
        return $this->belongsTo(CatlogManagement::class, 'catalog_id', 'id');
    }
    
    // Matches with 'parent_id'
    public function parentCatalog()
    {
        return $this->belongsTo(CatlogManagement::class, 'catalog_id', 'parent_id');
    }
    
}
