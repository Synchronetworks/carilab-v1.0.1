<?php

return [
    'app' => [
        'title' => 'General',
        'desc' => 'All the general settings for application.',
        'icon' => 'ph ph-cube',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'app_name', // unique name for field
                'label' => 'Bussiness Name', // you know what label it is
                'rules' => 'required|min:2|max:50', // validation rule of laravel
                'class' => '', // any class for input
                'value' => config('app.name'), // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'user_app_name', // unique name for field
                'label' => 'User App Name', // you know what label it is
                'rules' => 'required|min:2|max:50', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Kivilab', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'user_app_name', // unique name for field
                'label' => 'User App Name', // you know what label it is
                'rules' => 'required|min:2|max:50', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Kivilab', // default value if you want
                'datatype' => 'bussiness',
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'helpline_number', // unique name for field
                'label' => 'Contact Number', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '1234567890', // default value if you want
                'datatype' => 'bussiness',
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'inquriy_email', // unique name for field
                'label' => 'Email', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'admin@kivilab.com', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'short_description', // unique name for field
                'label' => 'Short Description', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Dummy Text ', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'google_analytics', // unique name for field
                'label' => 'Google Analytics', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'datatype' => 'misc',
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'logo', // unique name for field
                'label' => 'Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/logo.png', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'mini_logo', // unique name for field
                'label' => 'Mini Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/mini_logo.png', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'dark_logo', // unique name for field
                'label' => 'Dark Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'imageClass' => 'bg-dark',
                'value' => 'img/logo/dark_logo.png', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'light_logo', // unique name for field
                'label' => 'Lignt Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/logo.png', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'favicon', // unique name for field
                'label' => 'Favicon', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif,ico', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/mini_logo.png', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_line_1', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_line_2', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'country', // unique name for field
                'rules' => '', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'state', // unique name for field
                'rules' => '', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'city', // unique name for field
                'rules' => '', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_postal_code', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_latitude', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_longitude', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                'datatype' => 'bussiness',
            ],
        ],
    ],
    'module_setting' => [
        'title' => 'Module Settings',
        'desc' => 'Module Settings',
        'icon' => 'fas fa-chart-line',

        'elements' => [
           
          
            [
                'type' => 'checkbox',
                'data' => 'module_settings',
                'name' => 'is_multi_vendor',
                'label' => 'multi vendor',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
            ],


        ],

    ],

    'social' => [
        'title' => 'Social Profiles',
        'desc' => 'Link of all the social profiles.',
        'icon' => 'fas fa-users',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'facebook_url', // unique name for field
                'label' => 'Facebook Page URL', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'twitter_url', // unique name for field
                'label' => 'Twitter Profile URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'instagram_url', // unique name for field
                'label' => 'Instagram Account URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'linkedin_url', // unique name for field
                'label' => 'LinkedIn URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'youtube_url', // unique name for field
                'label' => 'Youtube Channel URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
        ],

    ],
    $misc = [
        'title' => 'Misc',
        'desc' => 'Application Data',
        'icon' => 'fas fa-globe-asia',
        'elements' => [
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'default_language',
                'label' => 'Language',
                'rules' => 'required',
                'class' => '',
                'value' => 'en',
                'datatype' => 'misc',
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'default_time_zone',
                'label' => 'Time Zone',
                'rules' => 'required',
                'class' => '',
                'value' => 'Asia/Kolkata',
                'datatype' => 'misc',
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'data_table_limit',
                'label' => 'Datatable Limit',
                'rules' => 'required',
                'class' => '',
                'value' => '10',
                'datatype' => 'misc',
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'is_enable_all',
                'label' => 'All',
                'rules' => '',
                'class' => '',
                'value' => '1',
            ],
            // Adding the remaining fields that are in the Blade form
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'google_analytics',
                'label' => 'Google Analytics',
                'rules' => '',
                'class' => '',
                'value' => '',  // Add your default value if needed
            ],
            [
                'type' => 'select',
                'data' => 'misc',
                'name' => 'default_currency',
                'label' => 'Default Currency',
                'rules' => 'required',
                'class' => 'select2js form-group country',
                'value' => '',  // Add your default value if needed
                'options' => [], // Populate this array dynamically
            ],
            [
                'type' => 'select',
                'data' => 'misc',
                'name' => 'currency_position',
                'label' => 'Currency Position',
                'rules' => 'required',
                'class' => 'select2js',
                'value' => 'left',  // Default value (you can change it to dynamic value as needed)
                'options' => [
                    'left' => 'Left',
                    'right' => 'Right',
                ],
            ],
            [
                'type' => 'select',
                'data' => 'misc',
                'name' => 'date_format',
                'label' => 'Date Format',
                'rules' => 'required',
                'class' => 'select2js date_format',
                'value' => '',  // Add your default value if needed
                'options' => dateFormatList(),  // Populate this dynamically
            ],
            [
                'type' => 'select',
                'data' => 'misc',
                'name' => 'time_format',
                'label' => 'Time Format',
                'rules' => 'required',
                'class' => 'select2js time_format',
                'value' => '',  // Add your default value if needed
                'options' => timeFormatList(),  // Populate this dynamically
            ],
            [
                'type' => 'checkbox',
                'data' => 'misc',
                'name' => 'android_app_links',
                'label' => 'Android App Links',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default to checked or unchecked as needed
            ],
            [
                'type' => 'checkbox',
                'data' => 'misc',
                'name' => 'ios_app_links',
                'label' => 'iOS App Links',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default to checked or unchecked as needed
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'latitude',
                'label' => 'Latitude',
                'rules' => '',
                'class' => '',
                'value' => '',  // Add your default value if needed
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'longitude',
                'label' => 'Longitude',
                'rules' => '',
                'class' => '',
                'value' => '',  // Add your default value if needed
            ],
            [
                'type' => 'select',
                'data' => 'misc',
                'name' => 'distance_type',
                'label' => 'Distance Type',
                'rules' => '',
                'class' => 'select2js',
                'value' => 'km',  // Default value
                'options' => [
                    'km' => 'Kilometer',
                    'mile' => 'Mile',
                ],
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'radious',
                'label' => 'Radius',
                'rules' => '',
                'class' => '',
                'value' => '50',  // Default value
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'digitafter_decimal_point',
                'label' => 'Decimal Points',
                'rules' => '',
                'class' => '',
                'value' => '2',  // Default value
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'settings_copyright',
                'label' => 'Copyright Text',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default value
            ],[
                'type' => 'text',
                'data' => 'misc',
                'name' => 'playstore_url',
                'label' => 'playstore Url',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default value
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'provider_playstore_url',
                'label' => 'Provider Playstore Url ',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default value
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'appstore_url',
                'label' => 'appstore Url',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default value provider_appstore_url
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'provider_appstore_url',
                'label' => 'provider Appstore rl',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default value  
            ],
            [
                'type' => 'text',
                'data' => 'misc',
                'name' => 'google_map_keys',
                'label' => 'Google Map Keys',
                'rules' => '',
                'class' => '',
                'value' => '',  // Default value  google_map_keys
            ],
        ],
    ],
    

    'notificationconfig' => [
    'title' => 'Notification Configuration',
    'desc' => 'Notification settings for various features',
    'icon' => 'fas fa-bell',

    'elements' => [

        [
            'type' => 'number', // input fields type
            'data' => 'notificationconfig', // data type, string, int, boolean
            'name' => 'expiry_plan', // unique name for field
            'label' => 'Expiry Plan', // you know what label it is
            'rules' => 'required|integer|min:0', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '1', // default value if you want
            'datatype' => 'notificationconfig',
        ],

        [
            'type' => 'number', // input fields type
            'data' => 'notificationconfig', // data type, string, int, boolean
            'name' => 'upcoming', // unique name for field
            'label' => 'Upcoming', // you know what label it is
            'rules' => 'required|integer|min:0', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '1', // default value if you want
            'datatype' => 'notificationconfig',
        ],

        [
            'type' => 'number', // input fields type
            'data' => 'notificationconfig', // data type, string, int, boolean
            'name' => 'continue_watch', // unique name for field
            'label' => 'Continue Watch', // you know what label it is
            'rules' => 'required|integer|min:0', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '1', // default value if you want
            'datatype' => 'notificationconfig',
        ],
    ],
],

    'analytics' => [
        'title' => 'Analytics',
        'desc' => 'Application Analytics',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'text', // data type, string, int, boolean
                'name' => 'google_analytics', // unique name for field
                'label' => 'Google Analytics (gtag)', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
                'datatype' => 'misc',
            ],
        ],

    ],

    'appconfig' => [
        'title' => 'App Configuration',
        'desc' => 'Settings for app configuration',
        'icon' => 'fas fa-chart-line',
        'elements' => [
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_social_login',
                'label' => 'Social Login',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable social media login.',
            ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_google_login',
                'label' => 'Google Login',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable login using Google accounts.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'google_client_id',
                'label' => 'Google Client ID',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Google Client ID for Google Login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'google_client_secret',
                'label' => 'Google Client Secret',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Google Client Secret for Google Login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'google_redirect_uri',
                'label' => 'Google Redirect URI',
                'rules' => 'required|nullable|url',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Redirect URI for Google Login.',
            ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_otp_login',
                'label' => 'OTP Login',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable login via OTP verification.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'apiKey',
                'label' => 'OTP API Key',
                'rules' => '',
                'class' => '',
                'value' => '',
                'help' => 'Enter the API Key for OTP login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'authDomain',
                'label' => 'OTP Auth Domain',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Auth Domain for OTP login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'databaseURL',
                'label' => 'OTP Database URL',
                'rules' => 'required|nullable|url',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Database URL for OTP login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'projectId',
                'label' => 'OTP Project ID',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Project ID for OTP login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'storageBucket',
                'label' => 'OTP Storage Bucket',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Storage Bucket for OTP login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'messagingSenderId',
                'label' => 'OTP Messaging Sender ID',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Messaging Sender ID for OTP login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'appId',
                'label' => 'OTP App ID',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the App ID for OTP login.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'measurementId',
                'label' => 'OTP Measurement ID',
                'rules' => 'required|nullable|string|max:255',
                'class' => '',
                'value' => '',
                'help' => 'Enter the Measurement ID for OTP login.',
            ],
            // [
            //     'type' => 'checkbox',
            //     'data' => 'appconfig',
            //     'name' => 'is_apple_login',
            //     'label' => 'Apple Login',
            //     'rules' => 'boolean',
            //     'class' => '',
            //     'value' => '0',
            //     'help' => 'Enable login using Apple ID.',
            // ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_firebase_notification',
                'label' => 'Firebase Notification',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable notifications via Firebase Cloud Messaging.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'firebase_key',
                'label' => 'Firebase Key',
                'rules' => 'required_if:is_firebase_notification,1',
                'class' => '',
                'value' => '',
                'help' => 'Paste your Firebase server key here.',
            ],
        
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_user_push_notification',
                'label' => 'User Push Notification',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable push notifications for users.',
            ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_vendor_push_notification',
                'label' => 'User Push Notification',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable push notifications for users.',
            ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_application_link',
                'label' => 'Application Links',
                'rules' => 'boolean',
                'class' => '',
                'value' => '',
                'help' => 'Enter URL for iOS app if applicable.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'ios_url',
                'label' => 'iOS App URL',
                'rules' => 'nullable|url',
                'class' => '',
                'value' => '',
                'help' => 'Enter URL for iOS app if applicable.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'android_url',
                'label' => 'Android App URL',
                'rules' => 'nullable|url',
                'class' => '',
                'value' => '',
                'help' => 'Enter URL for Android app if applicable.',
            ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'force_update',
                'label' => 'Force Update',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Force users to update the app.',
            ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'enter_app_version',
                'label' => 'Enter App Version',
                'rules' => 'required_if:force_update,1',
                'class' => '',
                'value' => '0',
                'help' => 'Force users to update the app.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'app_version',
                'label' => 'App Version',
                'rules' => 'required_if:force_update,1',
                'class' => '',
                'value' => '',
                'help' => 'Enter the current version of the app.',
            ],

            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'is_ChatGPT_integration',
                'label' => 'ChatGPT integration',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable integration via ChatGPT Cloud Messaging.',
            ],
            [
                'type' => 'checkbox',
                'data' => 'appconfig',
                'name' => 'test_without_key',
                'label' => 'ChatGPT integration',
                'rules' => 'boolean',
                'class' => '',
                'value' => '0',
                'help' => 'Enable integration via ChatGPT Cloud Messaging.',
            ],
            [
                'type' => 'text',
                'data' => 'appconfig',
                'name' => 'ChatGPT_key',
                'label' => 'ChatGPT Key',
                'rules' => 'required_if:is_ChatGPT_integration,1',
                'class' => '',
                'value' => '',
                'help' => 'Paste your ChatGPT server key here.',

            ],
        ],
    ],

    'custom_css' => [
        'title' => 'Custom Code',
        'desc' => 'Custom code area',
        'icon' => 'ph ph-barcode',

        'elements' => [
            [
                'type' => 'textarea', // input fields type
                'data' => 'customcode', // data type, string, int, boolean
                'name' => 'custom_css_block', // unique name for field
                'label' => 'Custom Css Code', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],
            [
                'type' => 'textarea', // input fields type
                'data' => 'customcode', // data type, string, int, boolean
                'name' => 'custom_js_block', // unique name for field
                'label' => 'Custom Js Code', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],
            

        ],

    ],
    'customization' => [
        'title' => 'Customization',
        'desc' => 'Setting on admin panel',
        'icon' => 'ph ph-barcode',
        'elements' => [
            [
                'type' => 'hidden', // input fields type
                'data' => 'customization', // data type, string, int, boolean
                'name' => 'customization_json', // unique name for field
                'label' => 'Customization', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '{}', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],
            [
                'type' => 'hidden', // input fields type
                'data' => 'customization', // data type, string, int, boolean
                'name' => 'root_colors', // unique name for field
                'label' => 'root_colors', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '{}', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],
        ],
    ],
    'mobile' => [
        'title' => 'Mobile',
        'desc' => 'Application Mobile',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'primary', // unique name for field
                'label' => 'Primary', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'secondary', // unique name for field
                'label' => 'Secondary', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
        ],

    ],

    'mail' => [
        'title' => 'Mail Setting',
        'desc' => 'Mail settings',
        'icon' => 'ph ph-envelope-simple',

        'elements' => [
            [
                'type' => 'email', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'email', // unique name for field
                'label' => 'Email', // you know what label it is
                'rules' => 'required|email', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'info@example.com', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_driver', // unique name for field
                'label' => 'Mail Driver', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'smtp', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_host', // unique name for field
                'label' => 'Mail Host', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'smtp.gmail.com', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_port', // unique name for field
                'label' => 'Mail Port', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '587', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_encryption', // unique name for field
                'label' => 'Mail Encryption', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'tls', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_username', // unique name for field
                'label' => 'Mail Username', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'youremail@gmail.com', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_password', // unique name for field
                'label' => 'Mail Password', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Password', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_from', // unique name for field
                'label' => 'Mail From', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'youremail@gmail.com', // default value if you wantPassword
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'from_name', // unique name for field
                'label' => 'From Name', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Kivilab-Laravel', // default value if you wantPassword
            ],
        ],

    ],
    'payment' => [
        'title' => 'Payment',
        'desc' => 'Payment',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'cashpayment', // data type, string, int, boolean
                'name' => 'cash_payment_method', // unique name for field
                'label' => 'Is Type ', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cash_payment_method', // data type, string, int, boolean
                'name' => 'cash_secretkey', // unique name for field
                'label' => 'cashpayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cash_payment_method', // data type, string, int, boolean
                'name' => 'cash_publickey', // unique name for field
                'label' => 'cashpayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'razorpayPayment', // data type, string, int, boolean
                'name' => 'razor_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'razor_payment_method', // data type, string, int, boolean
                'name' => 'razorpay_secretkey', // unique name for field
                'label' => 'razorpayPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'razor_payment_method', // data type, string, int, boolean
                'name' => 'razorpay_publickey', // unique name for field
                'label' => 'razorpayPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'stripePayment', // data type, string, int, boolean
                'name' => 'str_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'str_payment_method', // data type, string, int, boolean
                'name' => 'stripe_secretkey', // unique name for field
                'label' => 'stripePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'str_payment_method', // data type, string, int, boolean
                'name' => 'stripe_publickey', // unique name for field
                'label' => 'stripePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'paystackPayment', // data type, string, int, boolean
                'name' => 'paystack_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paystack_payment_method', // data type, string, int, boolean
                'name' => 'paystack_secretkey', // unique name for field
                'label' => 'paystackPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paystack_payment_method', // data type, string, int, boolean
                'name' => 'paystack_publickey', // unique name for field
                'label' => 'paystackPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'paypalPayment', // data type, string, int, boolean
                'name' => 'paypal_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paypal_payment_method', // data type, string, int, boolean
                'name' => 'paypal_secretkey', // unique name for field
                'label' => 'paypalPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paypal_payment_method', // data type, string, int, boolean
                'name' => 'paypal_clientid', // unique name for field
                'label' => 'paypalPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'flutterwavePayment', // data type, string, int, boolean
                'name' => 'flutterwave_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'flutterwave_payment_method', // data type, string, int, boolean
                'name' => 'flutterwave_secretkey', // unique name for field
                'label' => 'flutterwavePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'flutterwave_payment_method', // data type, string, int, boolean
                'name' => 'flutterwave_publickey', // unique name for field
                'label' => 'flutterwavePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            //
            [
                'type' => 'checkbox', // input fields type
                'data' => 'paymentcinet', // data type, string, int, boolean
                'name' => 'cinet_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cinet_payment_method', // data type, string, int, boolean
                'name' => 'cinet_siteid', // unique name for field
                'label' => 'paymentcinet', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cinet_payment_method', // data type, string, int, boolean
                'name' => 'cinet_api_key', // unique name for field
                'label' => 'paymentcinet', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cinet_payment_method', // data type, string, int, boolean
                'name' => 'cinet_Secret_key', // unique name for field
                'label' => 'paymentcinet', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            //sadad
            [
                'type' => 'checkbox', // input fields type
                'data' => 'paymentsadad', // data type, string, int, boolean
                'name' => 'sadad_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'sadad_payment_method', // data type, string, int, boolean
                'name' => 'sadad_Sadadkey', // unique name for field
                'label' => 'paymentsadad', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'sadad_payment_method', // data type, string, int, boolean
                'name' => 'sadad_id_key', // unique name for field
                'label' => 'paymentsadad', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'sadad_payment_method', // data type, string, int, boolean
                'name' => 'sadad_Domain', // unique name for field
                'label' => 'paymentsadad', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            //airtel_payment
            [
                'type' => 'checkbox', // input fields type
                'data' => 'airtelpayment', // data type, string, int, boolean
                'name' => 'airtel_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'datatype' => 'payment_gateways',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'airtel_payment_method', // data type, string, int, boolean
                'name' => 'airtel_money_secretkey', // unique name for field
                'label' => 'airtelpayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'airtel_payment_method', // data type, string, int, boolean
                'name' => 'airtel_money_client_id', // unique name for field
                'label' => 'airtelpayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

        //phonepe
        [
            'type' => 'checkbox', // input fields type
            'data' => 'phonepepayment', // data type, string, int, boolean
            'name' => 'phonepe_payment_method', // unique name for field
            'label' => 'Is Type', // you know what label it is
            'rules' => '', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '0', // default value if you want
            'datatype' => 'payment_gateways',
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'phonepe_payment_method', // data type, string, int, boolean
            'name' => 'phonepe_App_id', // unique name for field
            'label' => 'phonepepayment', // you know what label it is
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'phonepe_payment_method', // data type, string, int, boolean
            'name' => 'phonepe_Merchant_id', // unique name for field
            'label' => 'phonepepayment', // you know what label it is
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'phonepe_payment_method', // data type, string, int, boolean
            'name' => 'phonepe_salt_key', // unique name for field
            'label' => 'phonepepayment', // you know what label it is
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'phonepe_payment_method', // data type, string, int, boolean
            'name' => 'phonepe_salt_index', // unique name for field
            'label' => 'phonepepayment', // you know what label it is
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
        ],
        //min
        [
            'type' => 'checkbox', // input fields type
            'data' => 'midtranspayment', // data type, string, int, boolean
            'name' => 'midtrans_payment_method', // unique name for field
            'label' => 'Is Type', // you know what label it is
            'rules' => '', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '0', // default value if you want
            'datatype' => 'payment_gateways',
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'midtrans_payment_method', // data type, string, int, boolean
            'name' => 'midtrans_client_id', // unique name for field
            'label' => 'midtranspayment', // you know what label it is
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
        ],
        [
            'type' => 'checkbox', // input fields type
            'data' => 'inapppurchase', // data type, string, int, boolean
            'name' => 'iap_payment_method', // unique name for field
            'label' => 'In App Purchase', // label for the field
            'rules' => '', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '0', // default value if you want
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'iap_entertainment_id', // data type, string, int, boolean
            'name' => 'entertainment_id', // unique name for field
            'label' => 'Entertainment ID', // label for the field
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Enter the Entitlement ID for the In App Purchase.', // Help text for the input field
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'iap_apple_api_key', // data type, string, int, boolean
            'name' => 'apple_api_key', // unique name for field
            'label' => 'Apple API Key', // label for the field
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Enter the Apple API Key for the In App Purchase.', // Help text for the input field
        ],
        [
            'type' => 'text', // input fields type
            'data' => 'iap_google_api_key', // data type, string, int, boolean
            'name' => 'google_api_key', // unique name for field
            'label' => 'Google API Key', // label for the field
            'rules' => 'required|max:191', // validation rule of laravel
            'class' => '', // any class for input
            'value' => '', // default value if you want
            'help' => 'Enter the Google API Key for the In App Purchase.', // Help text for the input field
        ],


        ],
    ],

    'invoice_setting' => [
        'title' => 'Invoice Setting',
        'desc' => 'Order Related Setting.',
        'icon' => '',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'inv_prefix', // unique name for field
                'label' => 'lbl_order_prefix', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '# - ORDER', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'int', // data type, string, int, boolean
                'name' => 'order_code_start', // unique name for field
                'label' => 'lbl_order_starts', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '10001', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'spacial_note', // unique name for field
                'label' => 'lbl_spacial_note', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Thank you for visiting our store and choosing to make a purchase with us.', // default value if you want
            ],
        ],
    ],
    'other_settings' => [
        'title' => 'Other Settings',
        'desc' => 'Other Settings',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'is_event', // unique name for field
                'label' => 'Enable Events', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'google_login', // unique name for field
                'label' => 'Enable Google Login', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'apple_login', // unique name for field
                'label' => 'Enable Apple Login', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'otp_login', // unique name for field
                'label' => 'Enable Otp Login', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

        [
            'type' => 'checkbox',
            'data' => 'other_settings',
            'name' => 'whatsapp_notification',
            'label' => 'Enable WhatsApp Notification',
            'rules' => '',
            'class' => '',
            'value' => '0',
            'help' => 'Enable WhatsApp notifications via Twilio',
            'datatype' => 'notification',
        ],
        [
            'type' => 'text',
            'data' => 'other_settings',
            'name' => 'twilio_sid_whatsapp',
            'label' => 'Twilio SID (WhatsApp)',
            'rules' => 'required_if:whatsapp_notification,1',
            'class' => '',
            'value' => '',
            'help' => 'Enter your Twilio Account SID for WhatsApp',
            'datatype' => 'notification',
        ],
        [
            'type' => 'text',
            'data' => 'other_settings',
            'name' => 'twilio_auth_token_whatsapp',
            'label' => 'Twilio Auth Token (WhatsApp)',
            'rules' => 'required_if:whatsapp_notification,1',
            'class' => '',
            'value' => '',
            'help' => 'Enter your Twilio Auth Token for WhatsApp',
            'datatype' => 'notification',
        ],
        [
            'type' => 'text',
            'data' => 'other_settings',
            'name' => 'twilio_whatsapp_number',
            'label' => 'WhatsApp Number',
            'rules' => 'required_if:whatsapp_notification,1',
            'class' => '',
            'value' => '',
            'help' => 'Enter your Twilio WhatsApp number',
            'datatype' => 'notification',
        ],
        [
            'type' => 'checkbox',
            'data' => 'other_settings',
            'name' => 'sms_notification',
            'label' => 'Enable SMS Notification',
            'rules' => '',
            'class' => '',
            'value' => '0',
            'help' => 'Enable SMS notifications via Twilio',
            'datatype' => 'notification',
        ],
        [
            'type' => 'text',
            'data' => 'other_settings',
            'name' => 'twilio_sid_sms',
            'label' => 'Twilio SID (SMS)',
            'rules' => 'required_if:sms_notification,1',
            'class' => '',
            'value' => '',
            'help' => 'Enter your Twilio Account SID for SMS',
            'datatype' => 'notification',
        ],
        [
            'type' => 'text',
            'data' => 'other_settings',
            'name' => 'twilio_auth_token_sms',
            'label' => 'Twilio Auth Token (SMS)',
            'rules' => 'required_if:sms_notification,1',
            'class' => '',
            'value' => '',
            'help' => 'Enter your Twilio Auth Token for SMS',
            'datatype' => 'notification',
        ],
        [
            'type' => 'text',
            'data' => 'other_settings',
            'name' => 'twilio_phone_number_sms',
            'label' => 'SMS Phone Number',
            'rules' => 'required_if:sms_notification,1',
            'class' => '',
            'value' => '',
            'help' => 'Enter your Twilio phone number for SMS',
            'datatype' => 'notification',
        ],

        ],

    ],

    'storageconfig' => [
    'title' => 'Storage Settings',
    'desc' => 'Configuration settings for AWS S3 storage.',
    'icon' => 'fas fa-cloud',

    'elements' => [

        [
            'type' => 'checkbox', // input field type
            'data' => 'storage_settings', // data type, boolean
            'name' => 'local', // unique name for the field
            'label' => 'Enable Local Storage', // label for the field
            'rules' => 'boolean', // validation rules
            'class' => '', // any class for input
            'value' => 0, // default value
            'datatype' => 'storageconfig',
        ],
        [
            'type' => 'checkbox', // input field type
            'data' => 'storage_settings', // data type, boolean
            'name' => 's3', // unique name for the field
            'label' => 'Enable S3 Storage', // label for the field
            'rules' => 'boolean', // validation rules
            'class' => '', // any class for input
            'value' => 0, // default value
            'datatype' => 'storageconfig',
        ],

        [
            'type' => 'text', // input field type
            'data' => 'storage_settings', // data type, string
            'name' => 'aws_access_key', // unique name for the field
            'label' => 'AWS Access Key ID', // label for the field
            'rules' => '', // validation rules
            'class' => '', // any class for input
            'value' => '', // default value
            'datatype' => 'storageconfig',
        ],
        [
            'type' => 'text', // input field type
            'data' => 'storage_settings', // data type, string
            'name' => 'aws_secret_key', // unique name for the field
            'label' => 'AWS Secret Access Key', // label for the field
            'rules' => '', // validation rules
            'class' => '', // any class for input
            'value' => '', // default value
            'datatype' => 'storageconfig',
        ],
        [
            'type' => 'text', // input field type
            'data' => 'storage_settings', // data type, string
            'name' => 'aws_region', // unique name for the field
            'label' => 'AWS Default Region', // label for the field
            'rules' => '', // validation rules
            'class' => '', // any class for input
            'value' => '', // default value
            'datatype' => 'storageconfig',
        ],
        [
            'type' => 'text', // input field type
            'data' => 'storage_settings', // data type, string
            'name' => 'aws_bucket', // unique name for the field
            'label' => 'AWS Bucket', // label for the field
            'rules' => '', // validation rules
            'class' => '', // any class for input
            'value' => '', // default value
            'datatype' => 'storageconfig',
        ],
        [
            'type' => 'select', // input field type
            'data' => 'storage_settings', // data type, string
            'name' => 'aws_path_style', // unique name for the field
            'label' => 'AWS Use Path Style Endpoint', // label for the field
            'rules' => '', // validation rules
            'class' => '', // any class for input
            'value' => 'false', // default value
            'options' => ['false' => 'False', 'true' => 'True'], // select options
            'datatype' => 'storageconfig',
        ],

        // Add RazorpayX settings
    [
        'type' => 'checkbox',
        'data' => 'razorpayx_payment',
        'name' => 'razorpayx_payment_method',
        'label' => 'RazorpayX Payment',
        'rules' => '',
        'class' => '',
        'value' => '0',
        'datatype' => 'payment_gateways',
    ],
    [
        'type' => 'text',
        'data' => 'razorpayx_payment',
        'name' => 'razorpayx_secretkey',
        'label' => 'RazorpayX Secret Key',
        'rules' => 'required_if:razorpayx_payment_method,1',
        'class' => '',
        'value' => '',
        'help' => 'Enter RazorpayX Secret Key',
    ],
    [
        'type' => 'text',
        'data' => 'razorpayx_payment', 
        'name' => 'razorpayx_publickey',
        'label' => 'RazorpayX Public Key',
        'rules' => 'required_if:razorpayx_payment_method,1',
        'class' => '',
        'value' => '',
        'help' => 'Enter RazorpayX Public Key',
    ],
    [
        'type' => 'text',
        'data' => 'razorpayx_payment',
        'name' => 'razorpayx_account_number',
        'label' => 'RazorpayX Account Number',
        'rules' => 'required_if:razorpayx_payment_method,1',
        'class' => '',
        'value' => '',
        'help' => 'Enter RazorpayX Account Number',
    ],
    ],
],

'social_media' => [
    'title' => 'Social Media',
    'desc' => 'Social Media Links',
    'icon' => 'fas fa-share-alt',

    'elements' => [
        [
            'type' => 'text',
            'data' => 'text',
            'name' => 'facebook_url',
            'label' => 'Facebook URL',
            'rules' => 'nullable|url',
            'class' => 'form-control',
            'value' => '',
            'help' => 'Enter your Facebook page link.',
            'datatype' => 'misc',
        ],
        [
            'type' => 'text',
            'data' => 'text',
            'name' => 'twitter_url',
            'label' => 'Twitter URL',
            'rules' => 'nullable|url',
            'class' => 'form-control',
            'value' => '',
            'help' => 'Enter your Twitter profile link.',
            'datatype' => 'misc',
        ],
        [
            'type' => 'text',
            'data' => 'text',
            'name' => 'instagram_url',
            'label' => 'Instagram URL',
            'rules' => 'nullable|url',
            'class' => 'form-control',
            'value' => '',
            'help' => 'Enter your Instagram profile link.',
            'datatype' => 'misc',
        ],
        [
            'type' => 'text',
            'data' => 'text',
            'name' => 'linkedin_url',
            'label' => 'LinkedIn URL',
            'rules' => 'nullable|url',
            'class' => 'form-control',
            'value' => '',
            'help' => 'Enter your LinkedIn profile link.',
            'datatype' => 'misc',
        ],
        [
            'type' => 'text',
            'data' => 'text',
            'name' => 'youtube_url',
            'label' => 'YouTube URL',
            'rules' => 'nullable|url',
            'class' => 'form-control',
            'value' => '',
            'help' => 'Enter your YouTube channel link.',
            'datatype' => 'misc',
        ],
    ],


],

'commission_settings' => [
    'title' => 'Commission Settings',
    'desc' => 'Configure global and per-user commission settings.',
    'icon' => 'fa-solid fa-percentage',

    'elements' => [
        [
            'type' => 'text', // Radio button input type
            'data' => 'commission', // Data type: 1 for enabled, 0 for disabled
            'name' => 'vendor_commission_type', // Unique name for vendor commission type
            'label' => 'Vendor Commission Setup', // Label for radio button group
            'rules' => '', // Validation rule
            'class' => '', // CSS class if needed
            'value' => 'global', // Default value
            'help' => 'Select the commission setup type for vendors.', // Help text
            'datatype' => 'misc'
        ],
        [
            'type' => 'text', // Radio button input type
            'data' => 'commission', // Data type: 1 for enabled, 0 for disabled
            'name' => 'collector_commission_type', // Unique name for collector commission type
            'label' => 'Collector Commission Setup', // Label for radio button group
            'rules' => '', // Validation rule
            'class' => '', // CSS class if needed
            'value' => 'global', // Default value
            'help' => 'Select the commission setup type for collectors.', // Help text
            'datatype' => 'misc'
        ],
    ],
],



];
