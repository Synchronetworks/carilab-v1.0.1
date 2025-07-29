<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Modules\Currency\Models\Currency;
use Modules\Tax\Models\Tax;
use App\Models\MobileSetting;
use Modules\Subscriptions\Models\Subscription;
use App\Models\Device;

class SettingController extends Controller
{
    public function appConfiguraton(Request $request)
    {
        $settings = Setting::all()->pluck('val', 'name');

        $response = [];

        // Define the specific names you want to include
        $specificNames = ['app_name', 'footer_text', 'primary','razorpay_secretkey', 'razorpay_publickey', 'stripe_secretkey', 'stripe_publickey', 'paystack_secretkey', 'paystack_publickey', 'paypal_secretkey', 'paypal_clientid', 'flutterwave_secretkey', 'flutterwave_publickey', 'onesignal_app_id', 'onesignal_rest_api_key', 'onesignal_channel_id', 'google_maps_key', 'helpline_number', 'copyright', 'inquriy_email', 'site_description', 'customer_app_play_store', 'customer_app_app_store', 'isForceUpdate', 'version_code','cinet_siteid','cinet_api_key','cinet_Secret_key','sadad_Sadadkey','sadad_id_key','sadad_Domain','airtel_money_secretkey','airtel_money_client_id','phonepe_App_id','phonepe_Merchant_id','phonepe_salt_key','phonepe_salt_index','midtrans_client_id'];
        foreach ($settings as $name => $value) {
            if (in_array($name, $specificNames)) {
                if (strpos($name, 'onesignal_') === 0 && $request->is_authenticated == 1) {
                    $nestedKey = 'onesignal_customer_app';
                    $nestedName = str_replace('', 'onesignal_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'razorpay_') === 0 && $request->is_authenticated == 1 && $settings['razor_payment_method'] == 1) {
                    $nestedKey = 'razor_pay';

                    $nestedName = str_replace('', 'razorpay_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'stripe_') === 0 && $request->is_authenticated == 1 && $settings['str_payment_method'] == 1) {
                    $nestedKey = 'stripe_pay';
                    $nestedName = str_replace('', 'stripe_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'paystack_') === 0 && $request->is_authenticated == 1 && $settings['paystack_payment_method'] == 1) {
                    $nestedKey = 'paystack_pay';
                    $nestedName = str_replace('', 'paystack_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'paypal_') === 0 && $request->is_authenticated == 1 && $settings['paypal_payment_method'] == 1) {
                    $nestedKey = 'paypal_pay';
                    $nestedName = str_replace('', 'paypal_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'flutterwave_') === 0 && $request->is_authenticated == 1 && $settings['flutterwave_payment_method'] == 1) {
                    $nestedKey = 'flutterwave_pay';
                    $nestedName = str_replace('', 'flutterwave_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;

                }elseif (strpos($name, 'cinet_') === 0 && $request->is_authenticated == 1 && $settings['cinet_payment_method'] == 1) {
                    $nestedKey = 'cinet_pay';
                    $nestedName = str_replace('', 'cinet_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                }elseif (strpos($name, 'sadad_') === 0 && $request->is_authenticated == 1 && $settings['sadad_payment_method'] == 1) {
                    $nestedKey = 'sadad_pay';
                    $nestedName = str_replace('', 'sadad_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                }elseif (strpos($name, 'airtel_') === 0 && $request->is_authenticated == 1 && $settings['airtel_payment_method'] == 1) {
                    $nestedKey = 'airtel_pay';
                    $nestedName = str_replace('', 'airtel_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                }elseif (strpos($name, 'phonepe_') === 0 && $request->is_authenticated == 1 && $settings['phonepe_payment_method'] == 1) {
                    $nestedKey = 'phonepe_pay';
                    $nestedName = str_replace('', 'phonepe_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                }elseif (strpos($name, 'midtrans_') === 0 && $request->is_authenticated == 1 && $settings['midtrans_payment_method'] == 1) {
                    $nestedKey = 'midtrans_pay';
                    $nestedName = str_replace('', 'midtrans_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                }

                if (!strpos($name, 'onesignal_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'stripe_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'razorpay_') === 0) {
                    $response[$name] = $value;
                }
            }
        }
        // Fetch currency data
        $currency = \DB::table('countries')
        ->where('id', \App\Models\Setting::getSettings('default_currency'))->first();

        $currencyData = null;
        if ($currency) {

            $currencyData = [
                'currency_name' => $currency->currency_name ?? 'United States Dollar',
                'currency_symbol' => $currency->symbol ?? '$',
                'currency_code' => $currency->currency_code ?? 'currency_code',
                'currency_position' => isset($settings['currency_position']) ? $settings['currency_position'] : 'left',
                'no_of_decimal' => isset($settings['digitafter_decimal_point']) ? $settings['digitafter_decimal_point'] : '2',
                'thousand_separator' => ',',
                'decimal_separator' => '.',
            ];
        }

        $taxes = Tax::active()->get();

        if (isset($settings['isForceUpdate']) && isset($settings['version_code'])) {
            $response['isForceUpdate'] = intval($settings['isForceUpdate']);

            $response['version_code'] = intval($settings['version_code']);
        } else {
            $response['isForceUpdate'] = 0;

            $response['version_code'] = 0;
        }
       

        $response['tax'] = $taxes;

        $response['currency'] = $currencyData;
        $response['google_login_status'] = isset($settings['google_login']) ? (int)$settings['google_login'] : 0;
        $response['apple_login_status'] = isset($settings['apple_login']) ? (int)$settings['apple_login'] : 0;
        $response['otp_login_status'] = isset($settings['otp_login']) ? (int)$settings['otp_login'] : 0;
        $response['site_description'] = $settings['site_description'] ?? null;
        
        // Add locale language to the response
        $response['application_language'] = app()->getLocale();
        $response['status'] = true;
        $response['enable_in_app'] = isset($settings['iap_payment_method']) ? intval($settings['iap_payment_method']) : 0;
        $response['apple_api_key'] = isset($settings['apple_api_key']) ? $settings['apple_api_key'] : null;
        $response['google_api_key'] = isset($settings['google_api_key']) ? $settings['google_api_key'] : null;
        $response['is_multi_vendor'] = isset($settings['is_multi_vendor']) ? (int) $settings['is_multi_vendor'] : 0;
        $response['date_format'] = isset($settings['date_format']) ? $settings['date_format'] : null;
        $response['time_format'] = isset($settings['time_format']) ? $settings['time_format'] : null;
        $response['playstore_url'] = $settings['playstore_url'] ?? null;
        $response['appstore_url'] = $settings['appstore_url'] ?? null;
        $response['google_map_key'] = $settings['google_map_keys'] ?? null;
     
        return response()->json($response);
    }

    public function Configuraton(Request $request)
    {
        $googleMeetSettings = Setting::whereIn('name', ['google_meet_method', 'google_clientid', 'google_secret_key'])
            ->pluck('val', 'name');
        $settings = $googleMeetSettings->toArray();
        return $settings;
    }
}
