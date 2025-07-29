<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\Database\factories\VendorDocumentFactory;
use App\Models\BaseModel;
use Modules\Collector\Models\Collector;
use  App\Models\User;
use Modules\Document\Models\Document;
class VendorDocument extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = 'vendor_documents';
    protected $fillable = [
       'vendor_id','document_id','is_verified','status',
    ];

    protected $casts = [
        'vendor_id'   => 'integer',
        'document_id'   => 'integer',
        'is_verified'   => 'integer',
    ];

    public function vendors(){
        return $this->belongsTo('App\Models\User','vendor_id','id')->withTrashed();
    }   
    public function document(){
        return $this->belongsTo('Modules\Document\Models\Document','document_id','id')->withTrashed();
    }
    public function scopeMyDocument($query){
        $user = auth()->user();
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            $query =  $query->withTrashed();
        }

        if($user->hasRole('vendor')) {
            $query = $query->where('vendor_id', $user->id);
        }

        return  $query->whereHas('document',function ($q) {
            $q->where('status',1);
        });
    }
    public function scopeList($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }    
    }

