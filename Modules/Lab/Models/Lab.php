<?php

namespace Modules\Lab\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\Tax\Models\Tax;
use Modules\Lab\Trait\HasTaxList;
use Modules\CatlogManagement\Models\CatlogManagement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Review\Models\Review;
use Modules\Collector\Models\CollectorLabMapping;
use App\Models\Setting;

class Lab extends BaseModel implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasTaxList;
    protected $table = 'labs';
    protected $appends = ['feature_image', 'tax_list'];
    protected $fillable = [
        'name',
        'slug',
        'lab_code',
        'description',
        'vendor_id',
        'phone_number',
        'email',
        'logo_path',
        'address_line_1',
        'address_line_2',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'latitude',
        'longitude',
        'time_slot',
        'license_number',
        'license_document_path',
        'license_expiry_date',
        'accreditation_type',
        'accreditation_certificate_path',
        'accreditation_expiry_date',
        'tax_identification_number',
        'payment_modes',
        'payment_gateways',
        'status'
    ];

    protected $casts = [
        'payment_modes' => 'array',
        'payment_gateways' => 'array',
        'license_expiry_date' => 'date',
        'accreditation_expiry_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    const CUSTOM_FIELD_MODEL = 'Modules\Lab\Models\Lab';

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(\App\Models\User::class, 'vendor_id');
    }

    public function country()
    {
        return $this->belongsTo(\Modules\World\Models\Country::class);
    }

    public function state()
    {
        return $this->belongsTo(\Modules\World\Models\State::class);
    }

    public function city()
    {
        return $this->belongsTo(\Modules\World\Models\City::class);
    }
    public function labLocationMapping()
    {
        return $this->hasMany(\Modules\Lab\Models\LabLocationMapping::class, 'lab_id', 'id')->withTrashed();
    }
    public function labTaxMapping()
    {
        return $this->hasMany(\Modules\Lab\Models\LabTaxMapping::class, 'lab_id', 'id')->withTrashed();
    }


    // Media handling for logo
    public function getLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('logo') ? setBaseUrlWithFileName($this->getFirstMediaUrl('logo')) : setBaseUrlWithFileName();
    }

    // Media handling for license document
    public function getLicenseDocumentUrlAttribute()
    {
        return $this->getFirstMediaUrl('license_document');
    }


    public function getAccreditationCertificateUrlAttribute()
    {
        return $this->getFirstMediaUrl('accreditation_certificate');
    }

    // Status helper

    
    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');
        return isset($media) && ! empty($media) ? $media : '';
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str_replace(' ', '-', strtolower($value));
    }

    public function labSessions()
    {
        return $this->hasMany(\Modules\Lab\Models\LabSession::class, 'lab_id')->withTrashed();
    }
    public function testcase()
    {
        return $this->hasMany(CatlogManagement::class,'lab_id')->withTrashed();
    }
    public function getDistinctTestCaseCount()
    {
        return $this->testcase()->distinct()->count('id');
    }
    public function testpackage()
    {
        return $this->hasMany(PackageManagement::class,'lab_id')->withTrashed();
    }
    public function getDistinctTestPackageCount()
    {
        return $this->testpackage()->distinct()->count('id');
    }
    public function review()
    {
        return $this->hasMany(Review::class,'lab_id')->withTrashed();
    }
    public function getTotalReviews()
    {
        return $this->review()
        ->whereNotNull('lab_id')
        ->whereNull('deleted_by')
        ->count();  
    }
    public function getLabSession($data){
    
        $labData = [];

        foreach ($data as $session) {
            $breaks = $session->breaks ? json_decode($session->breaks, true) : [];

            $labData[] = [
                'day' => ucfirst($session->day),
                'status' => $session->is_holiday ? 'closed' : 'open',
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
                'breaks' => $breaks
            ];
        }
    
        $openDays = [];
        $holidays = [];
    
        foreach ($labData as $key => $day) {
            if ($day['status'] === 'closed') {
                $holidays[] = $day['day'];
            } else {
                if (!empty($openDays) &&
                    $day['start_time'] === $openDays[count($openDays) - 1]['start_time'] &&
                    $day['end_time'] === $openDays[count($openDays) - 1]['end_time'] &&
                    json_encode($day['breaks']) === json_encode($openDays[count($openDays) - 1]['breaks'])) {

                    $openDays[count($openDays) - 1]['day'] .= ', ' . $day['day'];
                } else {
                    $openDays[] = [
                        'day' => $day['day'],
                        'start_time' => $day['start_time'],
                        'end_time' => $day['end_time'],
                        'breaks' => $day['breaks']
                    ];
                }
            }
        }
    
        return [
            'open_days' => $openDays,
            'close_days' => $holidays
        ];
    }


    public function getNearestlab($latitude, $longitude)
    {
        $radius = Setting::getSettings('radious') ?? 50; // Default radius if setting is missing
    
        return Lab::selectRaw("*, 
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance", [$latitude, $longitude, $latitude])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having("distance", "<=", $radius)
            ->orderBy("distance", 'asc')
            ->get();
    }

    public function collectors()
    {
        return $this->hasMany(CollectorLabMapping::class, 'lab_id')->withTrashed();
    }

    public function scopeMyLabs(Builder $query)
    {
        $user = auth()->user(); 

        if ($user->hasRole('vendor')) {
            return $query->where('vendor_id', $user->id);
        }

        if ($user->hasRole('admin') || $user->hasRole('demo_admin') ) {
            return $query->withTrashed(); 
        }

        return $query;
    }
    protected static function booted()
{
    static::deleting(function ($lab) {
        if ($lab->isForceDeleting()) {
            $lab->labTaxMapping()->forceDelete();
            $lab->labLocationMapping()->forceDelete();
            $lab->testpackage()->forceDelete();
            $lab->testcase()->forceDelete();
            $lab->labSessions()->forceDelete();
            $lab->collectors()->forceDelete();
            $lab->review()->forceDelete();
            $lab->clearMediaCollection('logo');
            $lab->clearMediaCollection('license_document');
            $lab->clearMediaCollection('accreditation_certificate');
        } else {
            $lab->labTaxMapping()->delete();
            $lab->labLocationMapping()->delete();
            $lab->testpackage()->delete();
            $lab->testcase()->delete();
            $lab->labSessions()->delete();
            $lab->collectors()->delete();
            $lab->review()->delete();
        }
    });

    static::restoring(function ($lab) {
        $lab->labTaxMapping()->restore();
        $lab->labLocationMapping()->restore();
        $lab->testpackage()->restore();
        $lab->testcase()->restore();
        $lab->labSessions()->restore();
        $lab->collectors()->restore();
        $lab->review()->restore();
    });
}

}