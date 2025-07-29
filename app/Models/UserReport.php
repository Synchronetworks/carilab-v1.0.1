<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserReport extends BaseModel
{
    use SoftDeletes;
    protected $table = 'user_medical_reports';
    protected $fillable = ['user_id', 'name', 'uploaded_at', 'additional_notes'];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function getMedicalReportAttribute()
    {
        $media = $this->getFirstMediaUrl('medical_report');

        return isset($media) && ! empty($media) ? $media : asset(config('app.avatar_base_path').'avatar.webp');
    }
}
