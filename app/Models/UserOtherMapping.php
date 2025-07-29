<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class UserOtherMapping extends BaseModel implements HasMedia
{
    use SoftDeletes;
    protected $table = 'user_other_mapping';
    protected $fillable = ['user_id', 'first_name', 'last_name', 'gender', 'dob', 'phone', 'relation','email'];

    protected $appends = ['full_name', 'profile_image'];

    public function getFullNameAttribute() // notice that the attribute name is in CamelCase.
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function getProfileImageAttribute()
    {
        $media = $this->getFirstMediaUrl('profile_image');

        return isset($media) && ! empty($media) ? $media : asset(config('app.avatar_base_path').'avatar.webp');
    }
}
