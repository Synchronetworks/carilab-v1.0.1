<?php

namespace Modules\Setting\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Setting\Models\Setting;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Setting\Http\Requests\SettingRequest;
use App\Trait\ModuleTrait;
use Illuminate\Support\Facades\Config;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Currency\Models\Currency;
use Modules\World\Models\Country;
use Modules\User\Http\Requests\PasswordRequest;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;
use \RecursiveDirectoryIterator;
use \DirectoryIterator;
use Modules\Lab\Models\Lab;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Commision\Models\Commision;
use Modules\World\Models\State;
use Modules\World\Models\City;
class SettingsController extends Controller
{
    protected string $exportClass = '\App\Exports\SettingExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'settings.title', // module title
            'settings', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = __('messages.list');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ]
        ];
        $export_url = route('backend.settings.export');

        return view('setting::backend.setting.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url'));
    }


    public function generalSetting()
    {
        $fields = ['app_name', 'user_app_name', 'helpline_number', 'inquriy_email', 'short_description', 'logo', 'mini_logo', 'dark_logo', 'dark_mini_logo','light_logo', 'favicon'];
        $data = $this->fieldsData($fields);

        

        return view('setting::backend.setting.section-pages.general', compact('data'));
    }
    public function moduleSetting()
    {
        $fields = ['is_multi_vendor'];
        $settings = $this->fieldsData($fields);

        return view('setting::backend.setting.section-pages.module-setting', compact('settings'));
    }

    public function customCode()
    {
        $fields = ['custom_css_block', 'custom_js_block'];
        $data = $this->fieldsData($fields);
        return view('setting::backend.setting.section-pages.custom-code', compact('data'));
    }

    public function invoiceSetting()
    {
        $fields = ['inv_prefix', 'order_code_start', 'spacial_note'];
        $data = $this->fieldsData($fields);

        return view('setting::backend.setting.section-pages.invoice-setting', compact('data'));
    }

    public function customization()
    {
        $fields = ['customization_setting_1', 'customization_setting_2'];

        $data = $this->fieldsData($fields);
        return view('setting::backend.setting.section-pages.customization', compact('data'));
    }

    public function mail()
    {
        $fields = ['email', 'mail_driver', 'mail_host', 'mail_port', 'mail_encryption', 'mail_username', 'mail_from', 'from_name', 'mail_password'];
        $data = $this->fieldsData($fields);

        return view('setting::backend.setting.section-pages.mail-setting', compact('data'));
    }

    public function notificationSetting()
    {

        $query_data = NotificationTemplate::with('defaultNotificationTemplateMap', 'constant')->get();
        $notificationTemplates = [];
        $notificationKeyChannels = array_keys(config('notificationtemplate.channels'));
        $arr = [];
        // For Channel Map Or Update Channel Value
        foreach ($notificationKeyChannels as $key => $value) {
            $arr[$value] = 0;
        }
        foreach ($query_data as $key => $value) {
            $notificationTemplates[$key] = [
                'id' => $value->id,
                'type' => $value->type,
                'template' => $value->defaultNotificationTemplateMap->subject,
                'is_default' => false,
            ];
            if (isset($value->channels)) {
                $notificationTemplates[$key]['channels'] = $value->channels;
            } else {
                $notificationTemplates[$key]['channels'] = $arr;
            }
        }

        $channels = config('notificationtemplate.channels');

        return view('setting::backend.setting.section-pages.notification-setting', compact('channels', 'notificationTemplates'));
    }

    public function integration()
    {
        $fields = [
            'is_google_login',
            'is_one_signal_notification',
            'is_mobile_notification',
            'is_map_key',
            'isForceUpdate',
            'is_application_link',
            'is_custom_webhook_notification',
            'onesignal_app_id',
            'onesignal_rest_api_key',
            'onesignal_channel_id',
            'custom_webhook_content_key',
            'custom_webhook_url',
            'customer_app_play_store',
            'customer_app_app_store',
            'google_maps_key',
            'version_code',
            'google_secretkey',
            'google_publickey',
        ];
        $data = $this->fieldsData($fields);
        return view('setting::backend.setting.section-pages.integration', compact('data'));
    }

    public function otherSettings()
    {
        $fields = ['is_event', 'google_login','apple_login','otp_login', 'is_user_push_notification', 'is_vendor_push_notification', 'is_ChatGPT_integration', 'test_without_key', 'chatgpt_key', 'is_firebase_notification', 'firebase_key','is_multi_vendor','whatsapp_notification','twilio_sid_whatsapp','twilio_auth_token_whatsapp','twilio_whatsapp_number','sms_notification', 'twilio_sid_sms','twilio_auth_token_sms','twilio_phone_number_sms'];
        $settings = $this->fieldsData($fields);

        $directoryPath = storage_path('app/data');
        $jsonFile = '';

        if (File::isDirectory($directoryPath)) {
            $files = File::files($directoryPath);
            foreach ($files as $file) {
                if (strtolower($file->getExtension()) === 'json') {
                    $jsonFile = $file->getFilename(); // Get only file name
                    break;
                }
            }
        }

        $settings['json_file'] = $jsonFile;

        return view('setting::backend.setting.section-pages.other-settings', compact('settings'));
    }

    public function paymentMethod()
    {
        $fields = [ 'razorpayx_payment_method', 'razorpayx_secretkey','razorpayx_publickey','razorpayx_account_number','razor_payment_method', 'razorpay_secretkey', 'razorpay_publickey', 'str_payment_method', 'stripe_secretkey', 'stripe_publickey', 'paystack_payment_method', 'paystack_secretkey', 'paystack_publickey', 'paypal_payment_method', 'paypal_secretkey', 'paypal_clientid', 'flutterwave_payment_method', 'flutterwave_secretkey', 'flutterwave_publickey', 'cash_payment_method', 'cash_secretkey', 'cash_publickey', 'cinet_payment_method', 'cinet_siteid', 'cinet_api_key', 'cinet_Secret_key', 'sadad_payment_method', 'sadad_Sadadkey', 'sadad_id_key', 'sadad_Domain', 'airtel_payment_method', 'airtel_money_secretkey', 'airtel_money_client_id', 'phonepe_payment_method', 'phonepe_App_id', 'phonepe_Merchant_id', 'phonepe_salt_key', 'phonepe_salt_index', 'midtrans_payment_method', 'midtrans_client_id','iap_payment_method','entertainment_id','apple_api_key','google_api_key'];
        $settings = $this->fieldsData($fields);
        return view('setting::backend.setting.section-pages.payment-method', compact('settings'));
    }

    public function languageSettings()
    {
        $query_data = Config::get('app.available_locales');
        $languages = [];
        foreach ($query_data as $key => $value) {
            $languages[] = [
                'id' => $key,
                'name' => $value,
            ];
        }
        $fields = ['language_setting_1', 'language_setting_2'];
        $data = $this->fieldsData($fields);
        return view('setting::backend.setting.section-pages.language-settings', compact('data', 'languages'));
    }

    public function notificationConfiguration()
    {
        $fields = ['expiry_plan', 'upcoming', 'continue_watch'];
        $settings = $this->fieldsData($fields);
        return view('setting::backend.setting.section-pages.notification-configuration', compact('settings'));
    }

    public function currencySettings()
    {

        $currencies = Currency::all(); // Fetch all currencies, adjust query as needed

        $query_data = Country::get();

        $curr_names = [];
        foreach ($query_data as $row) {
            $curr_names[] = [
                'id' => $row->id,
                'name' => $row->name,
                'currency_name' => $row->currency_name,
                'symbol' => $row->symbol,
                'currency_code' => $row->currency_code,
            ];
        }


        return view('setting::backend.setting.section-pages.currency-setting', compact('currencies', 'curr_names'));
    }

    public function miscSetting()
    {
        $query_data = Config::get('app.available_locales');
        $languages = [];
        
        foreach ($query_data as $key => $value) {
            $languages[] = [
                'id' => $key,
                'name' => $value,
            ];
        }
        $timezones = timeZoneList();
        $data = [];
        $i = 0;
        foreach ($timezones as $key => $row) {
            $data[$i] = [
                'id' => $key,
                'text' => $row,
            ];
            $i++;
        }
        $timezones = $data;

        $fields = ['google_analytics', 'default_language', 'default_time_zone', 'data_table_limit', 'default_currency','date_format', 'time_format', 'time_zone', 'language_option', 'default_currency', 'currency_position', 'google_map_keys', 'latitude', 'longitude', 'distance_type', 'radious', 'digitafter_decimal_point', 'android_app_links', 'playstore_url', 'provider_playstore_url', 'ios_app_links', 'appstore_url', 'provider_appstore_url', 'settings_copyright'];
        $settings = $this->fieldsData($fields);
        
        $items = \DB::table('countries')->select(\DB::raw('id id,CONCAT(name , " ( " , symbol ," ) ") text'));
        $items->whereNotNull('symbol')->where('symbol', '!=', '');

        
        $currencies = $items->get();
        
      

        return view('setting::backend.setting.section-pages.misc-settings', compact('settings','currencies', 'languages', 'timezones'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */


    public function store(Request $request)
    {

        $data = $request->all();
        
        if (isset($data['vendor_commission_type'])&&$data['vendor_commission_type'] == 'global') {
            $this->vendorGlobalCommissions($userType='vendor');
        }  
        if(isset($data['collector_commission_type'])&&$data['collector_commission_type']=='global'){
            $this->vendorGlobalCommissions($userType='collector');
        }



        if ($request->has('json_file')) {
            $file = $request->file('json_file');
            $fileName = $file->getClientOriginalName();
            $directoryPath = storage_path('app/data');

            if (!File::isDirectory($directoryPath)) {
                File::makeDirectory($directoryPath, 0777, true, true);
            }

            $files = File::files($directoryPath);

            foreach ($files as $existingFile) {
                if (strtolower($existingFile->getExtension()) === 'json') {
                    File::delete($existingFile->getPathname());
                }
            }
            $file->move($directoryPath, $fileName);
         
        }

        unset($data['json_file']);
        $rules = Setting::getSelectedValidationRules(array_keys($request->all()));

        $data = $this->validate($request, $rules);
      
        $validSettings = array_keys($rules);

 
        foreach ($data as $key => $val) {
            if (in_array($key, $validSettings)) {
                $mimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/vnd.microsoft.icon'];
                if (gettype($val) == 'object') {
                    if ($val->getType() == 'file' && in_array($val->getmimeType(), $mimeTypes)) {
                        $setting = Setting::add($key, '', Setting::getDataType($key), Setting::getType($key));
                        $mediaItems = $setting->addMedia($val)->toMediaCollection($key);
                        $setting->update(['val' => $mediaItems->getUrl()]);
                    }
                } else {
                    $setting = Setting::add($key, $val, Setting::getDataType($key), Setting::getType($key));
                }
                if ($key === 'default_time_zone') {
                    Cache::forget('settings.default_time_zone');
    
                    // Fetch and apply the updated timezone
                    $newTimezone = $val;
                    Config::set('app.timezone', $newTimezone);
                    date_default_timezone_set($newTimezone);
                }
            }
        }

     
        $message = __('messages.save_setting');

        $message = '';
        $settingType = $setting->type ?? '';

        // Get the appropriate message based on setting type
        switch ($settingType) {
            case 'bussiness':
                $message = __('messages.general_setting_updated');
                break;
            case 'razorpayx_payment':
                $message = __('messages.payment_setting_updated');
                break;
            case 'mail_config':
                $message = __('messages.mail_setting_updated');
                break;
            case 'other_settings':
                $message = __('messages.app_setting_updated');
                break;
            case 'misc':
                $message = __('messages.misc_setting_updated');
                break;
            case 'commission':
                $message = __('messages.commission_setting_updated');
                break;
            case 'customcode':
                $message = __('messages.custom_code_setting_updated');
                break;
            case 'customization':
                $message = __('messages.customization_setting_updated');
                break;
            default:
                $message = __('messages.save_setting');
        }
        if ($request->wantsJson()) {

            return response()->json(['message' => $message, 'status' => true], 200);
        } else {
            return redirect()->back()->with('success', $message);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Setting::findOrFail($id);
        return view('setting::backend.setting.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(SettingRequest $request, Setting $setting)
    {
        $requestData = $request->all();
        $setting->update($requestData);
        $message=__('messages.update_setting');
        return redirect()->route('backend.settings.index', $setting->id)->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Setting::findOrFail($id);
        $data->delete();
        $message = __('messages.delete_form');
        return response()->json(['message' => $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Setting::withTrashed()->findOrFail($id);
        $data->restore();
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Setting::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }

    public function clear_cache()
    {
        Setting::flushCache();

        $message = __('messages.cache_cleard');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function fieldsData($fields)
    {

        $responseData = Setting::whereIn('name', $fields)->get();
        $data = [];

        foreach ($responseData as $setting) {
            $field = $setting->name;
            $value = $setting->val;

            if (in_array($field, ['logo', 'mini_logo', 'dark_logo', 'dark_mini_logo', 'favicon'])) {
                $value = asset($value);
            }

            $data[$field] = $value;
        }

        return $data;
    }

    public function change_password()
    {
        $module_title = __('messages.myprofile');
        return view('setting::backend.profile.section-pages.change-password', compact('module_title'));
    }

    public function information()
    {
        $user = Auth::user();
        $countries = Country::all(); // Get all countries
        $states = State::all(); // Get all states
        $cities = City::all(); // Get all cities
        $module_title = __('messages.myprofile');
        return view('setting::backend.profile.section-pages.information-page', compact('user','module_title','countries','states','cities'));

        $module_title = __('messages.myprofile');
        return view('setting::backend.profile.section-pages.information-page', compact('user','module_title'));
    }

    public function userProfileUpdate(Request $request)
    {
        $user = Auth::user();
        $data = User::findOrFail($user->id);
        $request_data = $request->except('profile_image');
        $data->update($request_data);

        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->file('profile_image')) {
            storeMediaFile($data, $request->file('profile_image'), 'profile_image');
        }

        $message = __('messages.profile_update');

        return redirect()->back()->with('success', $message);
    }

    public function changePassword(Request $request)
    {
        if (env('IS_DEMO')) {
            return redirect()->back()->with('error', __('messages.permission_denied'));
        }

        $user = Auth::user(); // Get the currently authenticated user
        $user_id = $user->id; // Retrieve the user's ID
        $data = User::findOrFail($user_id);

        if ($data == "") {
            $message = __('messages.user_not_found');
            return redirect()->back()->with('error', $message);
        }

        $hashedPassword = $data->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->new_password, $hashedPassword);

        if ($match) {
            if ($same_exits) {
                $message = __('messages.same_pass');
                return redirect()->back()->with('error', $message);
            }

            $data->fill([
                'password' => Hash::make($request->new_password)
            ])->save();
            $message = __('messages.pass_successfull');
            return redirect()->back()->with('success', $message);
        } else {
            $message = __('messages.check_old_password');
            return redirect()->back()->with('error', $message);
        }
    }



    // App config
    public function appConfig(Request $request)
    {
        $module_title = __('messages.add_title');
        $module_action = 'List';
        $fields = ['is_social_login', 'is_google_login','google_client_id','google_client_secret','google_redirect_uri', 'is_otp_login', 'apiKey', 'authDomain', 'databaseURL', 'projectId', 'storageBucket', 'messagingSenderId', 'appId', 'measurementId', 'is_apple_login', 'is_firebase_notification','firebase_key', 'is_user_push_notification', 'is_application_link', 'ios_url', 'android_url', 'force_update', 'enter_app_version', 'app_version', 'message_text', 'is_ChatGPT_integration', 'ChatGPT_key'];
        $data = $this->fieldsData($fields);
        return view('setting::backend.appconfig.index', compact('module_action', 'data', 'module_title'));
    }

    public function getCurrencyData(Request $request)
    {
        $currencyName = $request->input('currency_name');

        $currency = Country::where('currency_name', $currencyName)->first();

        if ($currency) {
            return response()->json([

                'currency_symbol' => $currency->symbol,
                'currency_code' => $currency->currency_code,
            ]);
        } else {
            return response()->json(['error' => __('messages.currency_not_found')], 404);
        }
    }
    public function socialMedia(Request $request)
    {
        $module_title = __('messages.add_title');
        $module_action = 'List';
        $fields = ['facebook_url','twitter_url','instagram_url','linkedin_url','youtube_url'];
        $socialmedia = $this->fieldsData($fields);
   
        return view('setting::backend.setting.section-pages.social-media', compact('module_action', 'socialmedia', 'module_title'));
    }

    public function commissionSetting()
    {
        $fields = ['vendor_commission_type', 'collector_commission_type'];
        $settings = $this->fieldsData($fields);

        return view('setting::backend.setting.section-pages.commision-setting', compact('settings'));
    }

    public function getFile(Request $request)
    {
        $requestLangData = $request->all();
        $filename = $requestLangData['file'];
        $requestLang = $requestLangData['lang']; 
        $langData = trans($filename, [], $requestLang);
    
        // Ensure langData is an array before proceeding
        if (!is_array($langData)) {
            return response()->json([
                'status' => __('messages.error'),
                'message' => __('messages.translation_file_not_return')
            ], 400);
        }
    
        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($langData),
            RecursiveIteratorIterator::SELF_FIRST
        );
    
        $path = [];
        $flatArray = [];
    
        foreach ($iterator as $key => $value) {
            $path[$iterator->getDepth()] = $key;
    
            if (!is_array($value)) {
                $flatArray[
                    implode('|', array_slice($path, 0, $iterator->getDepth() + 1))
                ] = $value;
            }
        }
    
        $data = view('setting::backend.setting.section-pages.components.lang_table', compact('flatArray', 'filename', 'requestLang'))->render();
            return response()->json( $data,200);
    }

    function saveFileContent(Request $request){
        $data = $request->all();
       
        $requestFile = $data['filename'] .'.php';


        $langDir = base_path().'/lang/';
        $filename = $langDir .$data['requestLang'] .'/' . $requestFile;
         
        unset($data['_token']);
        unset($data['filename']);
        unset($data['requestLang']);

        $data = flattenToMultiDimensional($data,"|");
        $fp = fopen($filename, 'w');
        fwrite($fp, var_export($data, true));
        File::prepend($filename, '<?php return  ');
        File::append($filename, ';');
        \Artisan::call('cache:clear');
        return redirect()->back()->withSuccess( __('messages.language_setting_updated'));
    }
    public function importData()
    {
        
        $labs = Lab::where('status', 1)->get();
        
        if(multivendor() == 1 && auth()->user()->user_type == 'vendor'){
            $labs = $labs->where('vendor_id',auth()->id())->get();
        }

        return view('setting::backend.setting.section-pages.import-data', compact('labs'));
    }
    
    public function vendorGlobalCommissions($userType='null')
    {
        \DB::beginTransaction(); // Start transaction
    
        try {
           
    
            // Get all active commissions
            $commissions = Commision::where('user_type',$userType)->get(); // Adjust query if filtering is needed
    
            foreach ($commissions as $commission) {
                $users = \App\Models\User::where('user_type', $userType)
                    ->whereNotExists(function ($query) use ($commission) {
                        $query->select(\DB::raw(1))
                            ->from('user_commission_mapping')
                            ->whereRaw('user_commission_mapping.user_id = users.id')
                            ->where('commission_id', $commission->id);
                    })->get();
    
                // Retrieve commission value from the commission table
                $commissionValue = $commission->value; // Assuming 'value' column stores commission amount
                $commissionType = $commission->type ?? 'global'; // Assuming 'type' column exists
    
                // Create new mappings for users who don't have one
                foreach ($users as $user) {
                    \App\Models\UserCommissionMapping::create([
                        'user_id' => $user->id,
                        'commission_id' => $commission->id,
                        'commission_type' => $commissionType,
                        'commission' => $commissionValue,
                        'created_by' => auth()->id()
                    ]);
                }
    
                // Update existing mappings
                \App\Models\UserCommissionMapping::where('commission_id', $commission->id)
                    ->update([
                        'commission_type' => $commissionType,
                        'commission' => $commissionValue
                    ]);
    
                $this->logActivity('update', $commission, 'commission_update');
            }
    
            \DB::commit(); // Commit transaction
    
            return redirect()->route('backend.vendor_commisions.index')->with('success', __('messages.record_update'));
        } catch (\Exception $e) {
            \DB::rollBack(); // Rollback transaction on failure
            return redirect()->back()->with('error', __('messages.update_failed') . ': ' . $e->getMessage());
        }
    }


}
