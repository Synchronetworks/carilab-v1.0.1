<?php

namespace Modules\Helpdesk\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Spatie\MediaLibrary\InteractsWithMedia;
class Helpdesk extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'help_desk';
    protected $fillable = [
    'subject','user_id','email','contact_number','mode','description','status','updated_by'
    ];
    protected $casts = [
        'user_id'               => 'integer',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Helpdesk\Models\Helpdesk';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */


    protected $appends = ['feature_image'];

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && ! empty($media) ? $media : '';
    }
   

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // Use User::class for better readability
    }

    // Define the relationship with HelpDeskActivityMapping model (hasMany)
    public function helpdeskactivity()
    {
        return $this->hasMany(Helpdeskactivity::class, 'helpdesk_id', 'id'); // Use HelpDeskActivityMapping::class
    }
}
