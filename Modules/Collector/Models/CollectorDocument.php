<?php

namespace Modules\Collector\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\Database\factories\VendorDocumentFactory;
use App\Models\BaseModel;

class CollectorDocument extends BaseModel
{
    use SoftDeletes;
    protected $table = 'collector_documents';
    protected $fillable = [
       'collector_id','document_id','is_verified','status'
    ];

    protected $casts = [
        'collector_id'   => 'integer',
        'document_id'   => 'integer',
        'is_verified'   => 'integer',
    ];

    public function collectors(){
        return $this->belongsTo('App\Models\User','collector_id','id')->withTrashed();
    }   
    public function document(){
        return $this->belongsTo('Modules\Document\Models\Document','document_id','id')->withTrashed();
    }
    public function scopeMyDocument($query){
        $user = auth()->user();
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            $query =  $query;
        }

        if($user->hasRole('collector')) {
            $query = $query->where('collector_id', $user->id);
        }

        return  $query->whereHas('document',function ($q) {
            $q->where('status',1);
        });
    }
    public function scopeList($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }    //return collectorDocumentFactory::new();
    public function collectorVendorMapping()
    {
        return $this->hasOne(CollectorVendorMapping::class, 'collector_id', 'collector_id');
    }
    }

