<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

if (!function_exists('isActive')) {
    /**
     * Returns 'active' or 'done' class based on the current step.
     *
     * @param  string|array  $route
     * @param  string  $className
     * @return string
     */
    function isActive($route, $className = 'active')
    {
        $currentRoute = Route::currentRouteName();

        if (is_array($route)) {
            return in_array($currentRoute, $route) ? $className : '';
        }

        return $currentRoute == $route ? $className : '';
    }
}
if (!function_exists('setting')) {
    function setting($key, $default = null)
    {

        if (is_null($key)) {
            return new App\Models\Setting();
        }

        if (is_array($key)) {
            return App\Models\Setting::set($key[0], $key[1]);
        }

        $value = App\Models\Setting::get($key);
        return is_null($value) ? value($default) : $value;
    }
}
function mail_footer($type)
{
    return [
        'notification_type' => $type,
        'logged_in_user_fullname' => auth()->user() ? auth()->user()->full_name ?? default_user_name() : '',
        'logged_in_user_role' => auth()->user() ? auth()->user()->getRoleNames()->first()->name ?? '-' : '',
        'company_name' => setting('app_name'),
        'company_contact_info' => implode('', [
            setting('helpline_number') . PHP_EOL,
            setting('inquriy_email'),
        ]),
    ];
}
function sendNotification($data)
{
    $mailable = \Modules\NotificationTemplate\Models\NotificationTemplate::where('type', $data['notification_type'])->with('defaultNotificationTemplateMap')->first();
    if ($mailable != null && $mailable->to != null) {
        $mails = json_decode($mailable->to);

        foreach ($mails as $key => $mailTo) {
            $data['type'] = $data['notification_type'];
            $subscription = isset($data['subscription']) ? $data['subscription'] : null;
            if (isset($subscription) && $subscription != null) {
                $data['id'] = $subscription['id'];
                $data['user_id'] = $subscription['user_id'];
                $data['plan_id'] = $subscription['plan_id'];
                $data['name'] = $subscription['name'];
                $data['identifier'] = $subscription['identifier'];
                $data['type'] = $subscription['type'];
                $data['status'] = $subscription['status'];
                $data['amount'] = $subscription['amount'];
                $data['plan_type'] = $subscription['plan_type'];
                $data['username'] = $subscription['user']->full_name;
                $data['notification_group'] = 'subscription';
                $data['site_url'] = env('APP_URL');

                unset($data['subscription']);
            }

            switch ($mailTo) {

                case 'admin':

                    $admin = \App\Models\User::role('admin')->first();

                    if (isset($admin->email)) {
                        try {
                            $admin->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }

                    break;
                case 'demo_admin':

                    $demoadmin = \App\Models\User::role('demo_Admin')->first();

                    if (isset($demoadmin->email)) {
                        try {
                            $demoadmin->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }

                    break;
                case 'user':
                    if (isset($data['user_id'])) {
                        $user = \App\Models\User::find($data['user_id']);
                        try {
                            $user->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }

                    break;

                case 'vendor':
                    if (isset($data['vendor_id'])) {
                        $user = \App\Models\User::find($data['vendor_id']);
                        try {
                            $user->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }

                    break;
                case 'collector':
                    if (isset($data['collector_id'])) {
                        $user = \App\Models\User::find($data['collector_id']);
                        try {
                            $user->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }

                    break;
            }
        }
    }
}
function sendNotifications($data)
{

    $heading = '#' . $data['id'] . ' ' . str_replace("_", " ", $data['name']);
    $content = strip_tags($data['description']);
    $appName = env('APP_NAME');
    $topic = str_replace(' ', '_', strtolower($appName));
    $type = $data['type'];
    $additionalData = json_encode($data);
    return fcm([

        "message" => [
            "topic" => $topic,
            "notification" => [
                "title" => $heading,
                "body" => $content,
            ],
            "data" => [
                "sound" => "default",
                "story_id" => "story_12345",
                "type" => $type,
                "additional_data" => $additionalData,
                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            ],
            "android" => [
                "priority" => "high",
                "notification" => [
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                ],
            ],
            "apns" => [
                "payload" => [
                    "aps" => [
                        "category" => $type,
                    ],
                ],
            ],
        ],

    ]);
}
function fcm($fields)
{

    $otherSetting = \App\Models\Setting::where('type', 'appconfig')->where('name', 'firebase_key')->first();


    $projectID = $otherSetting->val ?? null;

    Log::info($projectID);

    $access_token = getAccessToken();

    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];
    $ch = curl_init('https://fcm.googleapis.com/v1/projects/' . $projectID . '/messages:send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $response = curl_exec($ch);
    Log::info($response);
    curl_close($ch);
}
function getAccessToken()
{
    $directory = storage_path('app/data');
    $credentialsFiles = File::glob($directory . '/*.json');

    if (empty($credentialsFiles)) {
        throw new Exception(__('messages.no_json_file'));
    }
    $client = new Google_Client();
    $client->setAuthConfig($credentialsFiles[0]);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    $token = $client->fetchAccessTokenWithAssertion();

    return $token['access_token'];
}
/*
 * Global helpers file with misc functions.
 */
if (!function_exists('user_registration')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function user_registration()
    {
        $user_registration = false;

        if (env('USER_REGISTRATION') == 'true') {
            $user_registration = true;
        }

        return $user_registration;
    }
}


function dbConnectionStatus(): bool
{
    try {
        DB::connection()->getPdo();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function formatCurrency($number, $noOfDecimal, $decimalSeparator, $thousandSeparator, $currencyPosition, $currencySymbol)
{

    $formattedNumber = number_format($number, $noOfDecimal, '.', '');


    $parts = explode('.', $formattedNumber);
    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '';

    $integerPart = number_format($integerPart, 0, '', $thousandSeparator);


    $currencyString = '';

    if ($currencyPosition == 'left' || $currencyPosition == 'left_with_space') {
        $currencyString .= $currencySymbol;
        if ($currencyPosition == 'left_with_space') {
            $currencyString .= ' ';
        }

        $currencyString .= $integerPart;

        if ($noOfDecimal > 0) {
            $currencyString .= $decimalSeparator . $decimalPart;
        }
    }


    if ($currencyPosition == 'right' || $currencyPosition == 'right_with_space') {

        if ($noOfDecimal > 0) {
            $currencyString .= $integerPart . $decimalSeparator . $decimalPart;
        }
        if ($currencyPosition == 'right_with_space') {
            $currencyString .= ' ';
        }
        $currencyString .= $currencySymbol;
    }

    return $currencyString;
}


if (!function_exists('getImageUrlOrDefault')) {
    /**
     * Check if the image exists, return the file URL or the default image URL.
     *
     * @param string $fileUrl The full URL of the file to check
     * @return string The valid file URL or the default image URL
     */
    function getImageUrlOrDefault($fileUrl)
    {

        $fileUrl = setBaseUrlWithFileName($fileUrl);

        return $fileUrl;
    }
}

if (!function_exists('setDefaultImage')) {
    function setDefaultImage($fileUrl = '')
    {
        $defaultImagePath = '/default-image/Default-Image.jpg';
        $defaultImage = asset($defaultImagePath);

        if (empty($fileUrl)) {
            return $defaultImage;
        }

        return $fileUrl;
    }
}


function setBaseUrlWithFileName($url = '')
{

    if (empty($url)) {
        return setDefaultImage();
    }


    $isRemote = filter_var($url, FILTER_VALIDATE_URL) !== false;


    if ($isRemote) {

        return $url;

        return checkImageExists($url) ? $url : setDefaultImage();
    }


    $fileName = basename($url);
    $activeDisk = env('ACTIVE_STORAGE', 'local');


    if ($activeDisk === 'local') {
        $filePath = public_path("storage/kivilab-laravel/$fileName");


        if (file_exists($filePath)) {
            return asset("storage/kivilab-laravel/$fileName");
        }
    } else {

        $baseUrl = rtrim(env('DO_SPACES_URL'), '/');
        $filePath = "$baseUrl/kivilab-laravel/$fileName";


        if (checkImageExists($filePath)) {
            return $filePath;
        }
    }


    return setDefaultImage();
}


function formatDate($date)
{

    $releaseDate = Carbon::createFromFormat('Y-m-d', $date);
    $formattedDate = $releaseDate->format('jS F Y');
    return $formattedDate;
}

if (!function_exists('language_direction')) {
    /**
     * return direction of languages.
     *
     * @return string
     */
    function language_direction($language = null)
    {
        if (empty($language)) {
            $language = app()->getLocale();
        }
        $language = strtolower(substr($language, 0, 2));
        $rtlLanguages = [
            'ar',
            'arc',
            'bcc',
            'bqi',
            'ckb',
            'dv',
            'fa',
            'glk',
            'he',
            'lrc',
            'mzn',
            'pnb',
            'ps',
            'sd',
            'ug',
            'ur',
            'yi',
        ];
        if (in_array($language, $rtlLanguages)) {
            return 'rtl';
        }

        return 'ltr';
    }
}

function getCustomizationSetting($name, $key = 'customization_json')
{
    $settingObject = setting($key);
    if (isset($settingObject) && $key == 'customization_json') {
        try {
            $settings = (array) json_decode(html_entity_decode(stripslashes($settingObject)))->setting;

            return collect($settings[$name])['value'];
        } catch (\Exception $e) {
            return '';
        }

        return '';
    }

    return '';
}

/*
 * Get or Set the Settings Values
 *
 * @var [type]
 */
if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        if (is_null($key)) {
            return new App\Models\Setting();
        }

        if (is_array($key)) {
            return App\Models\Setting::set($key[0], $key[1]);
        }

        $value = App\Models\Setting::get($key);
        return is_null($value) ? value($default) : $value;
    }
}


/*
 * Global helpers file with misc functions.
 */
if (!function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return setting('app_name') ?? config('app.name');
    }
}

/**
 * Avatar Find By Gender
 */
if (!function_exists('default_user_avatar')) {
    function default_user_avatar()
    {
        return asset(config('app.avatar_base_path') . 'avatar.webp');
    }
    function default_user_name()
    {
        return __('messages.unknown_user');
    }
}
if (!function_exists('default_placeholder')) {
    function default_placeholder()
    {
        return asset('images/default-logo.png');
    }
}
function formatUpdatedAt($updatedAt)
{
    $diff = Carbon::now()->diffInHours($updatedAt);
    return $diff < 25 ? $updatedAt->diffForHumans() : $updatedAt->isoFormat('llll');
}


if (!function_exists('setDefaultImage')) {
    function setDefaultImage($fileUrl = '')
    {
        $defaultImagePath = '/default-image/Default-Image.jpg';
        $defaultImage = asset($defaultImagePath);

        if (empty($fileUrl)) {
            return $defaultImage;
        }

        return $fileUrl;
    }
}
function storeMediaFile($module, $files, $key = 'file_url')
{

    $module->clearMediaCollection($key);

    if (is_array($files)) {
        foreach ($files as $file) {
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = uniqid() . '.' . $extension;

                $module->addMedia($file)
                    ->usingFileName($fileName)
                    ->withCustomProperties([
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType()
                    ])
                    ->toMediaCollection($key);
            }
        }
    } else {
        $module->clearMediaCollection($key);

        $extension = $files->getClientOriginalExtension();
        $fileName = uniqid() . '.' . $extension;

        $module->addMedia($files)
            ->usingFileName($fileName)
            ->withCustomProperties([
                'original_name' => $files->getClientOriginalName(),
                'mime_type' => $files->getMimeType()
            ])
            ->toMediaCollection($key);
    }
}

function extractFileNameFromUrl($url = '')
{
    return basename(parse_url($url, PHP_URL_PATH));
}

function getFileExistsCheck($media)
{
    $mediaCondition = false;

    if ($media) {
        if ($media->disk == 'public') {
            $mediaCondition = file_exists($media->getPath());
        } else {
            $mediaCondition = \Storage::disk($media->disk)->exists($media->getPath());
        }
    }

    return $mediaCondition;
}

function getAttachments($attchments)
{
    $files = [];
    if (count($attchments) > 0) {
        foreach ($attchments as $attchment) {
            if (getFileExistsCheck($attchment)) {
                array_push($files, $attchment->getFullUrl());
            }
        }
    }

    return $files;
}

function getAttachmentArray($attchments)
{
    $files = [];
    if (count($attchments) > 0) {
        foreach ($attchments as $attchment) {
            if (getFileExistsCheck($attchment)) {
                $file = [
                    'id' => $attchment->id,
                    'url' => $attchment->getFullUrl()
                ];
                array_push($files, $file);
            }
        }
    }

    return $files;
}

function getSingleMedia($model, $collection = 'profile_image', $skip = true)
{
    if (!\Auth::check() && $skip) {
        return asset('images/user/user.png');
    }
    $media = null;
    if ($model !== null) {
        $media = $model->getFirstMedia($collection);
    }

    if (getFileExistsCheck($media)) {
        return $media->getFullUrl();
    } else {

        switch ($collection) {
            case 'image_icon':
                $media = default_user_avatar();
                break;
            case 'profile_image':
                $media = default_user_avatar();
                break;
            case 'service_attachment':
                $media = asset('images/default.png');
                break;
            case 'site_logo':
                $media = asset('images/logo.png');
                break;
            case 'site_favicon':
                $media = asset('images/favicon.png');
                break;
            case 'app_image':
                $media = asset('images/frontend/mb-serv-1.png');
                break;
            case 'app_image_full':
                $media = asset('images/frontend/mb-serv-full.png');
                break;
            case 'footer_logo':
                $media = asset('landing-images/logo/logo.png');
                break;
            case 'logo':
                $media = asset('images/logo.png');
                break;
            case 'favicon':
                $media = asset('images/favicon.png');
                break;
            case 'loader':
                $media = asset('images/loader.gif');
                break;
            case 'helpdesk_attachment':
                $media = asset('images/default.png');
                break;
            case 'helpdesk_activity_attachment':
                $media = default_user_avatar();
                break;
            default:
                $media = default_user_avatar();
                break;
        }
        return $media;
    }

    if (!function_exists('slug_format')) {
        /**
         * Format a string to Slug.
         */
        function slug_format($string)
        {
            $base_string = $string;

            $string = preg_replace('/\s+/u', '-', trim($string));
            $string = str_replace('/', '-', $string);
            $string = str_replace('\\', '-', $string);
            $string = strtolower($string);

            $slug_string = $string;

            return $slug_string;
        }
    }
}

function authSession($force = false)
{
    $session = new \App\Models\User;
    if ($force) {
        $user = \Auth::user()->getRoleNames();
        \Session::put('auth_user', $user);
        $session = \Session::get('auth_user');
        return $session;
    }
    if (\Session::has('auth_user')) {
        $session = \Session::get('auth_user');
    } else {
        $user = \Auth::user();
        \Session::put('auth_user', $user);
        $session = \Session::get('auth_user');
    }
    return $session;
}
function getMediaFileExit($model, $collection = 'profile_image')
{
    if ($model == null) {
        return asset('images/user/user.png');;
    }

    $media = $model->getFirstMedia($collection);

    return getFileExistsCheck($media);
}

if (!function_exists('imageExtention')) {
    function imageExtention($filePath)
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }
}


function formatOffset($offset)
{
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 and $minutes == 0) {
        $sign = ' ';
    }

    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT)
        . ':' . str_pad($minutes, 2, '0');
}

function timeZoneList()
{
    $list = \DateTimeZone::listAbbreviations();
    $idents = \DateTimeZone::listIdentifiers();

    $data = $offset = $added = [];
    foreach ($list as $abbr => $info) {
        foreach ($info as $zone) {
            if (!empty($zone['timezone_id']) and !in_array($zone['timezone_id'], $added) and in_array($zone['timezone_id'], $idents)) {
                $z = new \DateTimeZone($zone['timezone_id']);
                $c = new \DateTime(null, $z);
                $zone['time'] = $c->format('H:i a');
                $offset[] = $zone['offset'] = $z->getOffset($c);
                $data[] = $zone;
                $added[] = $zone['timezone_id'];
            }
        }
    }

    array_multisort($offset, SORT_ASC, $data);
    $options = [];
    foreach ($data as $key => $row) {
        $options[$row['timezone_id']] = $row['time'] . ' - ' . formatOffset($row['offset']) . ' ' . $row['timezone_id'];
    }

    return $options;
}
function getTimeInFormat($format)
{
    $now = new DateTime();
    $hours = $now->format('H');
    $minutes = $now->format('i');
    $seconds = $now->format('s');
    $milliseconds = $now->format('v');
    $totalSecondsSinceMidnight = ($hours * 3600) + ($minutes * 60) + $seconds;

    switch ($format) {
        case "H:i":
            return "$hours:$minutes";
        case "H:i:s":
            return "$hours:$minutes:$seconds";
        case "g:i A":
            $ampm = $hours >= 12 ? 'PM' : 'AM';
            $formattedHours = $hours % 12 || 12;
            return "$formattedHours:$minutes $ampm";
        case "H:i:s T":
            return "$hours:$minutes:$seconds UTC";
        case "H:i:s.v":
            return "$hours:$minutes:$seconds.$milliseconds";
        case "U":
            return $now->getTimestamp();
        case "u":
            return $milliseconds * 1000;
        case "G.i":
            return $hours + $minutes / 60;
        case "@BMT":
            $swatchBeat = floor($totalSecondsSinceMidnight / 86.4);
            return "@{$swatchBeat}BMT";
        default:
            return __('messages.invalid_format');
    }
}
function dateFormatList()
{
    return [
        'Y-m-d' => date('Y-m-d'),
        'm-d-Y' => date('m-d-Y'),
        'd-m-Y' => date('d-m-Y'),
        'd/m/Y' => date('d/m/Y'),
        'm/d/Y' => date('m/d/Y'),
        'Y/m/d' => date('Y/m/d'),
        'Y.m.d' => date('Y.m.d'),
        'd.m.Y' => date('d.m.Y'),
        'm.d.Y' => date('m.d.Y'),
        'jS M Y' => date('jS M Y'),
        'M jS Y' => date('M jS Y'),
        'D, M d, Y' => date('D, M d, Y'),
        'D, d M, Y' => date('D, d M, Y'),
        'D, M jS Y' => date('D, M jS Y'),
        'D, jS M Y' => date('D, jS M Y'),
        'F j, Y' => date('F j, Y'),
        'd F, Y' => date('d F, Y'),
        'jS F, Y' => date('jS F, Y'),
        'l jS F Y' => date('l jS F Y'),
        'l, F j, Y' => date('l, F j, Y'),

    ];
}

function timeFormatList()
{
    $timeFormats = [
        "H:i",
        "H:i:s",
        "g:i A",
        "H:i:s T",
        "H:i:s.v",
        "U",
        "u",
        "G.i",
        "@BMT"
    ];

    return array_map(function ($format) {
        return ['format' => $format, 'time' => getTimeInFormat($format)];
    }, $timeFormats);
}


function flattenToMultiDimensional(array $array, $delimiter = '.')
{
    $result = [];
    foreach ($array as $notations => $value) {

        $keys = explode($delimiter, $notations);

        $keys = array_reverse($keys);


        $lastVal = $value;
        foreach ($keys as $key) {

            $lastVal = [
                $key => $lastVal
            ];
        }


        $result = array_merge_recursive($result, $lastVal);
    }

    return $result;
}

function comman_custom_response($response, $status_code = 200)
{
    return response()->json($response, $status_code);
}


function multivendor()
{

    return setting('is_multi_vendor');
}


function isSmtpConfigured()
{
    $host = env('MAIL_HOST');
    $port = env('MAIL_PORT');
    $username = env('MAIL_USERNAME');
    $password = env('MAIL_PASSWORD');

    return !empty($host) &&
        !empty($port) &&
        !empty($username) &&
        !empty($password) &&
        strtolower($username) !== 'null' &&
        strtolower($password) !== 'null';
}


function admin_id()
{
    $user = User::getUserByKeyValue('user_type', 'admin');
    return $user->id;
}

function get_user_name($user_id)
{
    $name = '';
    $user = \App\Models\User::getUserByKeyValue('id', $user_id);
    if ($user !== null) {
        $name = $user->full_name;
    }
    return $name;
}

function timeAgoFormate($date)
{
    if ($date == null) {
        return '-';
    }

    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();

    return $diff_time;
}

function GetpaymentMethod($name)
{

    if ($name) {
        $payment_key = Setting::where('name', $name)->value('val');
        return $payment_key !== null ? $payment_key : null;
    }
    return null;
}

function GetcurrentCurrency($currencyCode = null)
{


    if (!$currencyCode) {

        $currencyCode = Setting::getSettings('default_currency') ?? null;
    }


    $currency = DB::table('countries')
        ->where('id', $currencyCode)
        ->select('currency_code')
        ->first();


    return $currency ? ($currency->currency_code) : 'USD';
}

function getCurrencySymbol($currencyCode = null)
{
    if (!$currencyCode) {

        $currencyCode = Setting::getSettings('default_currency') ?? null;
    }


    $currency = DB::table('countries')
        ->where('id', $currencyCode) // Assuming `id` is used for currency identification
        ->select('symbol as currency_symbol')
        ->first();


    return $currency->currency_symbol ?? '$';
}

function applyExcelStyles(Worksheet $sheet)
{

    $rowCount = $sheet->getHighestRow();

    $columnCount = $sheet->getHighestColumn();

    $totalColumns = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnCount);


    foreach (range('A', $columnCount) as $col) {

        $width = 10;


        if ($totalColumns <= 3) {
            $width = 22;
        } else if ($totalColumns <= 5) {
            $width = 15;
        } else if ($totalColumns <= 7) {
            $width = 10;
        } else if ($totalColumns <= 10) {
            $width = 2;
        } elseif ($totalColumns <= 20) {
            $width = 5;
        }

        $sheet->getColumnDimension($col)->setWidth($width);
    }

    $sheet->getStyle("A1:{$columnCount}1")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        ],
    ]);

    $sheet->getStyle("A1:{$columnCount}{$rowCount}")
        ->getAlignment()->setHorizontal('center')
        ->setVertical('center');

    $sheet->getStyle("A1:{$columnCount}{$rowCount}")->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ]);


    for ($row = 1; $row <= $rowCount; $row++) {
        $sheet->getRowDimension($row)->setRowHeight(25);
    }

    $sheet->freezePane('A2');

    $pageSetup = $sheet->getPageSetup();


    $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


    $pageSetup->setFitToWidth(1);
    $pageSetup->setFitToHeight(0);

    $sheet->getPageMargins()->setTop(0.5)
        ->setBottom(0.5)
        ->setLeft(0.5)
        ->setRight(0.5);


    $sheet->getPageSetup()->setPrintArea("A1:{$columnCount}{$rowCount}");
}
function getPaymentStatusAttribute($payment_status = 'pending')
{
    switch ($payment_status) {
        case 'paid':
            return '<span class="badge bg-success-subtle">' . __('messages.paid') . '</span>';
            break;
        case 'pending':
            return '<span class="badge bg-warning-subtle">' . __('messages.pending') . '</span>';
            break;
        case 'pending_by_collector':
            return '<span class="badge bg-secondary-subtle">' . __('messages.pending_by_collector') . '</span>';
            break;
        case 'approved_by_collector':
            return '<span class="badge bg-primary-subtle">' . __('messages.approved_by_collector') . '</span>';
            break;
        case 'send_to_vendor':
            return '<span class="badge bg-info-subtle">' . __('messages.send_to_vendor') . '</span>';
            break;
        case 'approved_by_vendor':
            return '<span class="badge bg-primary-subtle">' . __('messages.approved_by_vendor') . '</span>';
            break;
        case 'send_to_admin':
            return '<span class="badge bg-info-subtle">' . __('messages.send_to_admin') . '</span>';
            break;

        default:
            return '<span class="badge bg-danger-subtle">' . __('messages.failed') . '</span>';
            break;
    }
}

function GetSettingValue($key)
{

    if ($key) {
        $data = Setting::where('name', $key)->value('val');
        return $data !== null ? $data : null;
    }
    return null;
}
