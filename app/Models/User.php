<?php

namespace App\Models;

use App\Models\Presenters\UserPresenter;
use App\Models\Traits\HasHashedMediaTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Review\Models\Review;
use Modules\Subscriptions\Models\Subscription;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Laravolt\Avatar\Facade as Avatar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
use App\Models\UserTaxMapping;
use Modules\Collector\Models\Collector;
use Modules\Collector\Models\CollectorVendorMapping;
use Modules\Collector\Models\CollectorLabMapping;
use Modules\Vendor\Models\VendorDocument;
use Modules\Collector\Models\CollectorDocument;
use App\Models\UserOtherMapping;
use Modules\Appointment\Models\AppointmentCollectorMapping;
use Modules\Commision\Models\CommissionEarning;
use Illuminate\Database\Eloquent\Builder;
use Modules\Bank\Models\Bank;
use App\Trait\EarningTrait;
use Modules\Appointment\Models\Appointment;
use Modules\Lab\Models\Lab;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;
use App\Models\UserReport;
use Modules\Wallet\Models\Wallet;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{

    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use HasHashedMediaTrait;
    use UserPresenter;
    use HasApiTokens;
    use EarningTrait;

    const CUSTOM_FIELD_MODEL = 'App\Models\User';

    protected $guarded = [
        'id',
        'updated_at',
        '_token',
        '_method',
        'password_confirmation',
    ];

    protected $dates = [
        'deleted_at',
        'date_of_birth',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'user_setting' => 'array',
    ];

    protected $appends = [
        'full_name',
        'profile_image',
        'total_commission_amount',
        'total_appointments',
        'total_service_amount',
        'total_tax_amount',
        'total_admin_earnings',
        'total_vendor_earnings',
        'total_collector_earnings',
        'collector_paid_earnings',
        'vendor_paid_earnings'
    ];

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'login_type',
        'gender',
        'date_of_birth',
        'email_verified_at',
        'password',
        'is_verify',
        'is_banned',
        'is_subscribe',
        'status',
        'set_as_featured',
        'last_notification_seen',
        'user_type',
        'remember_token',
        'is_available',
        'last_online_time',
        'social_image',
    ];

    public function getFullNameAttribute() // notice that the attribute name is in CamelCase.
    {
        return $this->first_name . ' ' . $this->last_name;
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providers()
    {
        return $this->hasMany('App\Models\UserProvider');
    }

    /**
     * Get the list of users related to the current User.
     *
     * @return [array] roels
     */
    public function getRolesListAttribute()
    {
        return array_map('intval', $this->roles->pluck('id')->toArray());
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return env('SLACK_NOTIFICATION_WEBHOOK');
    }

    /**
     * Get all of the service_providers for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptionPackage()
    {
        return $this->hasOne(Subscription::class, 'user_id', 'id')
            ->where('status', config('constant.SUBSCRIPTION_STATUS.ACTIVE'))
            ->latestOfMany();
    }

    public function subscriptionPackageList()
    {

        return $this->hasMany(Subscription::class, 'user_id', 'id')
            ->where('status', config('constant.SUBSCRIPTION_STATUS.ACTIVE'))->orderBy('id', 'desc');
    }

    public function subscriptiondata()
    {
        return $this->hasMany(Subscription::class, 'user_id', 'id')->orderBy('start_date', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('is_banned', 0);
    }


    public function scopeCalenderResource($query)
    {
        $query->where('show_in_calender', 1);
    }


    public function getProfileImageAttribute()
    {
        $media = $this->getFirstMediaUrl('profile_image');
        return $media ?: asset(config('app.avatar_base_path') . 'avatar.webp');
    }


    public function scopeSetRole($query, $user)
    {

        $user_id = $user->id;
        auth()->user()->hasRole(['admin', 'demo_admin', 'user', 'vendor', 'collector']);
    }


    public function userTaxMapping()
    {
        return $this->hasMany(UserTaxMapping::class, 'user_id', 'id');
    }
    public function userCommissionMapping()
    {
        return $this->hasMany(UserCommissionMapping::class, 'user_id', 'id')->with('commissions')->withTrashed();
    }
    public function userReport()
    {
        return $this->hasMany(UserReport::class, 'user_id', 'id')->withTrashed();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function collector()
    {
        return $this->hasOne(Collector::class, 'user_id', 'id')->withTrashed();
    }

    public function collectorVendormapping()
    {
        return $this->hasOne(CollectorVendorMapping::class, 'collector_id')->withTrashed();
    }
    public function vendorCollectormapping()
    {
        return $this->hasMany(CollectorVendorMapping::class, 'vendor_id')->whereNull('deleted_at');
    }
    public function lab()
    {
        return $this->hasOne(CollectorLabMapping::class, 'collector_id')->with('lab')->withTrashed();
    }
    public function vendorDocument()
    {
        return $this->hasMany(vendorDocument::class, 'vendor_id', 'id');
    }

    public function collectorDocument()
    {
        return $this->hasMany(CollectorDocument::class, 'collector_id', 'id');
    }
    public function otherMapping()
    {
        return $this->hasMany(UserOtherMapping::class, 'user_id', 'id');
    }

    public function collectorAppointmentmapping()
    {
        return $this->hasMany(AppointmentCollectorMapping::class, 'collector_id', 'id');
    }
    public function vendorappointment()
    {
        return $this->hasMany(Appointment::class, 'vendor_id', 'id');
    }
    public function userappointment()
    {
        return $this->hasMany(Appointment::class, 'customer_id', 'id');
    }
    public function commission_earning()
    {
        return $this->hasMany(CommissionEarning::class, 'employee_id');
    }
    protected function getUserByKeyValue($key, $value)
    {
        return $this->where($key, $value)->first();
    }

    public function scopeMyCollector(Builder $query)
    {
        $user = auth()->user();

        if ($user && $user->hasRole('vendor')) {

            $query = $query->where('user_type', 'collector')->whereHas('collectorVendormapping', function ($query) use ($user) {
                $query->where('vendor_id', $user->id);
            });
        }

        if ($user && $user->hasRole('collector')) {

            return $query->where('id', $user->id);
        }
        if ($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->where('user_type', 'collector')->withTrashed();
        }


        return $query;
    }
    public function scopeMyVendor(Builder $query)
    {
        $user = auth()->user();

        if ($user && $user->hasRole('vendor')) {
            $query = $query->where('id', $user->id);
        }

        if ($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed();
        }

        return $query;
    }

    public function banks()
    {
        return $this->hasMany(Bank::class, 'user_id', 'id');
    }
    public function getAppointmentStats($userId, $userType)
    {
        $field = $userType === 'collector' ? 'collector_id' : 'vendor_id';

        if ($userType === 'vendor') {
            return [
                'total_appointments' => Appointment::where($field, $userId)->count(),
                'cancelled_appointments' => Appointment::where($field, $userId)
                    ->whereIn('status', ['cancelled', 'rejected'])
                    ->count(),
                'completed_appointments' => Appointment::where($field, $userId)
                    ->where('status', 'completed')
                    ->count(),
                'upcoming_appointments' => Appointment::where($field, $userId)
                    ->whereIn('status', ['pending', 'accept'])
                    ->count(),
            ];
        }

        // For collectors, use `appointmentCollectorMapping` relation
        return [
            'total_appointments' => Appointment::whereHas('appointmentCollectorMapping', function ($q) use ($userId, $field) {
                $q->where($field, $userId);
            })->count(),

            'cancelled_appointments' => Appointment::whereHas('appointmentCollectorMapping', function ($q) use ($userId, $field) {
                $q->where($field, $userId);
            })->whereIn('status', ['cancelled', 'rejected'])->count(),

            'completed_appointments' => Appointment::whereHas('appointmentCollectorMapping', function ($q) use ($userId, $field) {
                $q->where($field, $userId);
            })->where('status', 'completed')->count(),

            'upcoming_appointments' => Appointment::whereHas('appointmentCollectorMapping', function ($q) use ($userId, $field) {
                $q->where($field, $userId);
            })->whereIn('status', ['pending', 'accept'])->count(),
        ];
    }
    public function vendorLabs()
    {
        return $this->hasMany(Lab::class, 'vendor_id', 'id')->whereNull('deleted_at');
    }
    public function vendorTestCases()
    {
        return $this->hasMany(CatlogManagement::class, 'vendor_id', 'id')->whereNull('deleted_at');
    }
    public function vendorTestPackages()
    {
        return $this->hasMany(PackageManagement::class, 'vendor_id', 'id')->whereNull('deleted_at');
    }
    public function labLimitReach()
    {
        $subscriptionPackage = $this->subscriptionPackage;
        if (!$subscriptionPackage) {
            // Handle cases where the vendor does not have a subscription package
            return true; // Or false, based on your application's logic
        }

        $planLimitation = $subscriptionPackage->plan->planLimitation->firstWhere('limitation_slug', 'number-of-laboratories');
        if (!$planLimitation || $planLimitation->limitation_value != 1) {
            // Handle cases where the plan does not have the 'number-of-laboratories' limitation
            return true; // Or false, based on your application's logic
        }

        $labCount = $this->vendorLabs()
            ->where('created_at', '>=', $subscriptionPackage->start_date)
            ->count();

        return $labCount >= $planLimitation->limit;
    }
    public function collectorLimitReach()
    {
        $subscriptionPackage = $this->subscriptionPackage;

        if (!$subscriptionPackage) {
            // Handle cases where the vendor does not have a subscription package
            return true; // Or false, based on your application's logic
        }

        $planLimitation = $subscriptionPackage->plan->planLimitation->firstWhere('limitation_slug', 'number-of-collectors');
        if (!$planLimitation || $planLimitation->limitation_value != 1) {
            // Handle cases where the plan does not have the 'number-of-collectors' limitation
            return true; // Or false, based on your application's logic
        }

        $labCount = $this->vendorCollectormapping()
            ->where('created_at', '>=', $subscriptionPackage->start_date)
            ->count();
        return $labCount >= (int) $planLimitation->limit;
    }
    public function testCaseLimitReach()
    {
        $subscriptionPackage = $this->subscriptionPackage;

        if (!$subscriptionPackage) {
            // Handle cases where the vendor does not have a subscription package
            return true; // Or false, based on your application's logic
        }

        $planLimitation = $subscriptionPackage->plan->planLimitation->firstWhere('limitation_slug', 'number-of-test-case');
        if (!$planLimitation || $planLimitation->limitation_value != 1) {
            // Handle cases where the plan does not have the 'number-of-test-case' limitation
            return true; // Or false, based on your application's logic
        }

        $labCount = $this->vendorTestCases()
            ->where('created_at', '>=', $subscriptionPackage->start_date)
            ->count();
        return $labCount >= (int) $planLimitation->limit;
    }
    public function testPackageLimitReach()
    {
        $subscriptionPackage = $this->subscriptionPackage;

        if (!$subscriptionPackage) {
            // Handle cases where the vendor does not have a subscription package
            return true; // Or false, based on your application's logic
        }

        $planLimitation = $subscriptionPackage->plan->planLimitation->firstWhere('limitation_slug', 'number-of-test-package');
        if (!$planLimitation || $planLimitation->limitation_value != 1) {
            // Handle cases where the plan does not have the 'number-of-test-case' limitation
            return true; // Or false, based on your application's logic
        }

        $labCount = $this->vendorTestPackages()
            ->where('created_at', '>=', $subscriptionPackage->start_date)
            ->count();
        return $labCount >= (int) $planLimitation->limit;
    }
    public function enableWhatsappNotification()
    {
        $subscriptionPackage = $this->subscriptionPackage;

        // If no subscription package exists, allow or deny based on business logic
        if (!$subscriptionPackage) {
            return false;
        }

        // Fetch the plan limitation for 'enable-whatsapp-notification'
        $planLimitation = $subscriptionPackage->plan->planLimitation
            ->firstWhere('limitation_slug', 'enable-whatsapp-notification');

        // If there's no such limitation or its value is not enabled (1), return false
        if (!$planLimitation || $planLimitation->limitation_value != 1) {
            return false;
        }

        return true; // Allow WhatsApp notification if all conditions are met
    }
    public function enableSMSNotification()
    {
        $subscriptionPackage = $this->subscriptionPackage;

        // If no subscription package exists, allow or deny based on business logic
        if (!$subscriptionPackage) {
            return false;
        }

        // Fetch the plan limitation for 'enable-whatsapp-notification'
        $planLimitation = $subscriptionPackage->plan->planLimitation
            ->firstWhere('limitation_slug', 'enable-sms-notification');

        // If there's no such limitation or its value is not enabled (1), return false
        if (!$planLimitation || $planLimitation->limitation_value != 1) {
            return false;
        }

        return true; // Allow WhatsApp notification if all conditions are met
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'collector_id', 'id');
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id')->where('status', 1);
    }
    public function scopeMyUser(Builder $query)
    {
        $user = auth()->user();

        if ($user && $user->hasRole('vendor')) {
            $query = $query->whereHas('userappointment', function ($q) use ($user) {
                $q->where('vendor_id', $user->id);
            });
        }

        if ($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query->withTrashed();
        }

        return $query;
    }
}
