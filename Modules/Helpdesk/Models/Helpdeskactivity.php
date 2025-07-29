<?php

namespace Modules\Helpdesk\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Helpdesk\Database\factories\HelpdeskactivityFactory;
use App\Models\User;
use Modules\Helpdesk\Models\Helpdesk;
class Helpdeskactivity extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
  
    protected $table = 'help_desk_activity_mapping';
    protected $fillable = [ 'helpdesk_id', 'sender_id', 'receiver_id', 'messages','activity_type','activity_message'];

    protected $casts = [
        'helpdesk_id'    => 'integer',
        'sender_id'      => 'integer',
        'receiver_id'  => 'integer',
    ];

    public function receiver(){
        return $this->belongsTo(User::class,'receiver_id', 'id')->withTrashed();
    }
    public function sender(){
        return $this->belongsTo(User::class,'sender_id', 'id')->withTrashed();
    }
    public function HelpDesk(){
        return $this->belongsTo(Helpdesk::class,'helpdesk_id', 'id')->withTrashed();
    }
}
