<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ language_direction() }}" class="theme-fs-sm">
<head>
    <script>
        const storedTheme = sessionStorage.getItem('theme_mode') || 'light';
        document.documentElement.setAttribute('data-bs-theme', storedTheme);
        document.documentElement.setAttribute('data-bs-theme-color', '{{ getCustomizationSetting('theme_color') }}');
    </script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset(setting('logo')) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset(setting('favicon')) }}">
    <meta name="keyword" content="{{ setting('meta_keyword') }}">
    <meta name="description" content="{{ setting('meta_description') }}">
    <meta name="setting_options" content="{{ setting('customization_json') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ env('APP_URL') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="data_table_limit" content="{{ setting('data_table_limit') }}">
    
    
</head>
<!-- Shortcut Icon -->
<link rel="shortcut icon" href="{{ asset(setting('favicon')) }}">
<link rel="icon" type="image/ico" href="{{ asset(setting('favicon')) }}" />


<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="app_name" content="{{ app_name() }}">

<meta name="data_table_limit" content="{{ setting('data_table_limit') }}">


<meta name="auth_user_roles" content="{{ auth()->user()->roles->pluck('name') }}">


<title> @yield('title')  </title>

<link rel="stylesheet" href="{{ mix('css/icon.min.css') }}">

@stack('before-styles')
<link rel="stylesheet" href="{{ mix('css/libs.min.css') }}">
<link rel="stylesheet" href="{{ mix('css/backend.css') }}">
<link rel="stylesheet" href="{{ mix('css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('custom-css/dashboard.css') }}">

@if (language_direction() == 'rtl')
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
@endif

<link rel="stylesheet" href="{{ asset('css/customizer.css') }}">


<style>
    :root {
        <?php
        $rootColors = setting('root_colors'); // Assuming the setting() function retrieves the JSON string
        
        // Check if the JSON string is not empty and can be decoded
        if (!empty($rootColors) && is_string($rootColors)) {
            $colors = json_decode($rootColors, true);
        
            // Check if decoding was successful and the colors array is not empty
            if (json_last_error() === JSON_ERROR_NONE && is_array($colors) && count($colors) > 0) {
                foreach ($colors as $key => $value) {
                    echo $key . ': ' . $value . '; ';
                }
            } 
        }
        ?>
    }
</style>

<!-- Scripts -->
<!-- Scripts -->
@php
    $currentLang = App::currentLocale();
    $langFolderPath = base_path("lang/$currentLang");
    $filePaths = \File::files($langFolderPath);
@endphp

@foreach ($filePaths as $filePath)
    @php
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);

        $arr = require $filePath;
    @endphp
    <script>
        window.localMessagesUpdate = {
            ...window.localMessagesUpdate,
            "{{ $fileName }}": @json($arr)
        }
    </script>
@endforeach

@php
    $role = !empty(auth()->user()) ? auth()->user()->getRoleNames() : null;
    $permissions = auth()->user()->getAllPermissions()->pluck('name')->toArray();
@endphp
<script>
    window.auth_role = @json($role);
    window.auth_permissions = @json($permissions)
</script>
<!-- Google Font -->

<link rel="stylesheet" href="{{ asset('iconly/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('phosphor-icons/regular/style.css') }}">
<link rel="stylesheet" href="{{ asset('phosphor-icons/fill/style.css') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
@stack('after-styles')

<x-google-analytics />

<style>
    {!! setting('custom_css_block') !!}
</style>
</head>

<body class="{{ !empty(getCustomizationSetting('card_style')) ? getCustomizationSetting('card_style') : '' }}">
    <!-- Loader Start -->
    <div id="loading">
        <x-partials._body_loader />
    </div>
    <!-- Loader End -->
    <!-- Sidebar -->
    @if (!isset($noLayout) || !$noLayout)
        @include('backend.includes.sidebar')
    @endif

    <!-- /Sidebar -->

    @yield('navbars')

    <div class="main-content wrapper">
        @if (!isset($noLayout) || !$noLayout)

            <div class="position-relative pr-hide
{{ !isset($isBanner) ? 'iq-banner' : '' }} default">
                <!-- Header -->
                @include('backend.includes.header')
                <!-- /Header -->
                @if (!isset($isBanner))
                    <!-- Header Banner Start-->

                    @include('components.partials.sub-header')

                    <!-- Header Banner End-->
                @endif
            </div>
        @endif

        <div class="conatiner-fluid content-inner pb-0" id="page_layout">
            <!-- Main content block -->
            @yield('content')
            <!-- / Main content block -->
            @if (isset($export_import) && $export_import)
                <div data-render="import-export">
                    <export-modal export-url="{{ $export_url }}"
                        :module-column-prop="{{ json_encode($export_columns) }}" module-name="{{ $module_name }}"></export-modal>
                    <import-modal></import-modal>
                </div>
            @endif
        </div>



    </div>
    <!-- Footer block -->
    @if (!isset($noLayout) || !$noLayout)
        @include('backend.includes.footer')
        @include('backend.layouts._dynamic_script')
    @endif
    <!-- / Footer block -->

    <!-- Modal -->
    <div class="modal fade" data-iq-modal="renderer" id="renderModal" tabindex="-1" aria-labelledby="renderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" data-iq-modal="content">
                <div class="modal-header">
                    <h5 class="modal-title" data-iq-modal="title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" data-iq-modal="body">
                    <p>Modal body text goes here.</p>
                </div>
            </div>
        </div>
    </div>





    @stack('before-scripts')
    <script src="{{ mix('js/backend.js') }}"></script>
    <script src="{{ asset('js/iqonic-script/utility.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ mix('js/import-export.min.js') }}"></script>
    @if (isset($assets) && (in_array('textarea', $assets) || in_array('editor', $assets)))
        <script src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
        <script src="{{ asset('vendor/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
    @endif

    {{-- // Mobile number input field --}}
    <script src="{{ mix('js/intlTelInput.min.js') }}"  type="module"></script>
    <script src="{{ mix('js/utils.js') }}" type="module"></script>
    <link rel="stylesheet" href="{{ asset('css/intl-tel-input.css') }}">
    <script src="{{ asset('js/intl-tel-input.js') }}"  type="module"></script>

    <script>
        function initializeIntlTelInput(selector) {
            var input = document.querySelector(selector);
            if (input) {
                var iti = window.intlTelInput(input, {
                    initialCountry: "in", // Set initial country
                    separateDialCode: true, // Show country code separately
                    utilsScript: "{{ asset('js/utils.js') }}"
                });
    
                // Update input value properly
                function updateInputValue() {
                    let fullNumber = iti.getNumber();
                    if (iti.isValidNumber()) {
                        input.value = fullNumber; // Set formatted number only if valid
                    }
                }
    
                // Update on blur (when clicking outside the field)
                input.addEventListener('blur', updateInputValue);
    
                // Prevent number from clearing when switching fields
                input.addEventListener('change', function () {
                    if (iti.getNumber()) {
                        input.value = iti.getNumber();
                    }
                });
    
                // Handle country change correctly
                input.addEventListener("countrychange", function () {
                    let number = input.value.trim();
                    if (number) {
                        let newNumber = iti.getNumber();
                        input.value = newNumber;
                    }
                });
            }
        }
    
        // Initialize for both fields
        initializeIntlTelInput("#mobile");
        initializeIntlTelInput("#phone_number");
    </script>    

    {{-- Password visibility toggle --}}
    <script>
        function togglePasswordVisibility(id) {
            var input = document.getElementById(id);
            var icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

    @stack('after-scripts')
    <!-- / Scripts -->
    <script>
        $('.notification_list').on('click', function() {
            notificationList();
        });

        $(document).on('click', '.notification_data', function(event) {
            event.stopPropagation();
        })

        function notificationList(type = '') {
            var url = "{{ route('notification.list') }}";
            $.ajax({
                type: 'get',
                url: url,
                data: {
                    'type': type
                },
                success: function(res) {
                    $('.notification_data').html(res.data);
                    getNotificationCounts();
                    if (res.type == "markas_read") {
                        notificationList();
                    }
                    $('.notify_count').removeClass('notification_tag').text('');
                }
            });
        }

        function setNotification(count) {
            if (Number(count) >= 100) {
                $('.notify_count').text('99+');
            }
        }

        function getNotificationCounts() {
            var url = "{{ route('notification.counts') }}";

            $.ajax({
                type: 'get',
                url: url,
                success: function(res) {
                    if (res.counts > 0) {
                        $('.notify_count').addClass('notification_tag').text(res.counts);
                        setNotification(res.counts);
                        $('.notification_list span.dots').addClass('d-none')
                        $('.notify_count').removeClass('d-none')
                    } else {
                        $('.notify_count').addClass('d-none')
                        $('.notification_list span.dots').removeClass('d-none')
                    }

                    if (res.counts <= 0 && res.unread_total_count > 0) {
                        $('.notification_list span.dots').removeClass('d-none')
                    } else {
                        $('.notification_list span.dots').addClass('d-none')
                    }
                }
            });
        }

        getNotificationCounts();
    </script>
    {{-- form validation script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-submit');
            const submitButton = document.getElementById('submit-button');
            let formSubmitted = false;
            if (form) {
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    field.addEventListener('input', () => validateField(field));
                    field.addEventListener('change', () => validateField(field));
                });
                form.addEventListener('submit', function(event) {
                    if (formSubmitted) {
                        event.preventDefault();
                        return;
                    }

                    const isValid = validateForm();

                    if (!isValid) {
                        event.preventDefault();
                        submitButton.disabled = false;
                        formSubmitted = false; // Reset the flag
                        return;
                    }

                    submitButton.disabled = true;
                    submitButton.innerText = 'Loading...';
                    formSubmitted = true;
                });
            }

            function validateForm() {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });
                // Optional: Check for URL pattern validation
                const urlInput = form.querySelector('input[name="video_url_input"]');
                if (urlInput && urlInput.required && urlInput.value.trim() && !/^https?:\/\/.+/.test(urlInput
                        .value)) {
                    isValid = false;
                }
                const emailInput = form.querySelector('input[type="email"]');
                if (emailInput && emailInput.required && emailInput.value.trim() && !isValidEmail(emailInput
                        .value)) {
                    isValid = false;
                    showValidationError(emailInput, 'Enter a valid Email Address.');
                }
                const mobileInput = form.querySelector('input[name="mobile"]', );
                if (mobileInput && mobileInput.required && mobileInput.value.trim() && !validatePhoneNumber(
                        mobileInput.value)) {
                    isValid = false;
                    showValidationError(mobileInput, 'Enter a valid contact number.');
                }
                const phone_numberInput = form.querySelector('input[name="phone_number"]', );
                if (phone_numberInput && phone_numberInput.required && phone_numberInput.value.trim() && !validatePhoneNumber(
                    phone_numberInput.value)) {
                    isValid = false;
                    showValidationError(phone_numberInput, 'Enter a valid contact number.');
                }
                const helplineInput = form.querySelector('input[name="helpline_number"]');
                if (helplineInput && helplineInput.required && helplineInput.value.trim() && !validatePhoneNumber(
                        helplineInput.value)) {
                    isValid = false;
                    showValidationError(helplineInput, 'Enter a valid helpline number.');
                }

                const inquiryemailInput = form.querySelector('input[name="inquriy_email"]');
                if (inquiryemailInput && inquiryemailInput.required && inquiryemailInput.value.trim() && !
                    isValidEmail(inquiryemailInput.value)) {
                    isValid = false;
                    showValidationError(inquiryemailInput, 'Enter a valid Inquiry email address.');
                }
                return isValid;
            }

            function showValidationError(input, message) {
                const errorFeedback = input.nextElementSibling;
                if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
                    errorFeedback.textContent = message;
                    input.classList.add('is-invalid');
                }
            }

            function validateField(field) {
                const fieldId = field.id; // Use id for error message display
                const fieldError = document.getElementById(`${fieldId}-error`);
                let isValid = true;

                if (!field.value.trim()) {
                    if (fieldError) {
                        fieldError.style.display = 'block';
                    }
                    isValid = false;
                } else {
                    if (fieldError) {
                        fieldError.style.display = 'none';
                    }
                }

                return isValid;
            }

            function isValidEmail(email) {
                // Simple regex for email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function validatePhoneNumber(phoneNumber) {
                const phonePattern = /^[\d\s\-,+]+$/; // Allows digits, spaces, hyphens, commas, and plus signs
                return phonePattern.test(phoneNumber);
            }

        });
    </script>
    <script>
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.requires-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    // Check if TinyMCE is defined before calling triggerSave
                    if (typeof tinymce !== 'undefined') {
                        tinymce.triggerSave();
                    }

                    // Check form validity
                    let isValid = form.checkValidity();

                    if (!isValid) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });

        })();
    </script>
    {{-- description tinymce set --}}
    <script>
        function initTinyMCE(selector) {
            tinymce.init({
                selector: selector,
                plugins: 'link image code',
                toolbar: 'undo redo | styleselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | removeformat | code | image',
                setup: function (editor) {
                    // Setup TinyMCE to listen for changes
                    editor.on('change', function () {
                        // Get the editor content
                        const content = editor.getContent().trim();
                        const $textarea = $(editor.getElement()); // Get the associated textarea
                        const $error = $(`#${$textarea.attr('id')}-error`); // Find the error element by ID

                        // Check if content is empty
                        if (content === '') {
                            $textarea.addClass('is-invalid'); // Add invalid class if empty
                            $error.show(); // Show validation message
                        } else {
                            $textarea.removeClass('is-invalid'); // Remove invalid class if not empty
                            $error.hide(); // Hide validation message
                        }
                    });
                }
            });
        }

        // Initialize TinyMCE for multiple selectors if they exist
        const tinyMCESelectors = ['#description', '#instructions', '#additional_notes', '#restrictions', '#symptoms'];
        tinyMCESelectors.forEach(selector => {
            if (document.querySelector(selector)) {
                initTinyMCE(selector);
            }
        });
    </script>


    <script>
        {!! setting('custom_js_block') !!}
        @if (\Session::get('error'))
            Swal.fire({
                title: 'Error',
                text: '{{ session()->get('error') }}',
                icon: "error",
                showClass: {
                    popup: 'animate__animated animate__zoomIn'
                },
                hideClass: {
                    popup: 'animate__animated animate__zoomOut'
                }
            })
        @endif

        // dark and light mode code
        const theme_mode = sessionStorage.getItem('theme_mode')
        const element = document.querySelector('html');
        if (theme_mode === null) {
            element.setAttribute('data-bs-theme', 'light')
        } else {
            document.documentElement.setAttribute('data-bs-theme', theme_mode)
        }
    </script>
    <script src="{{ asset('vendor/flatpickr/flatpicker.min.js') }}"></script>
    <script>
        // Ensure Laravel finds the Setting model correctly
        window.defaultCurrencySymbol = @json(\DB::table('countries')
            ->where('id', \App\Models\Setting::getSettings('default_currency'))
            ->value('symbol') ?? '$');
    
        const currencyFormat = (amount) => {
            const DEFAULT_CURRENCY = {
                no_of_decimal: @json(\App\Models\Setting::getSettings('digitafter_decimal_point') ?? 2),
                decimal_separator: '.',
                thousand_separator: ',',
                currency_position: @json(\App\Models\Setting::getSettings('currency_position') ?? 'left'),
                currency_symbol: window.defaultCurrencySymbol 
            };
    
            return formatCurrency(
                amount, 
                DEFAULT_CURRENCY.no_of_decimal, 
                DEFAULT_CURRENCY.decimal_separator, 
                DEFAULT_CURRENCY.thousand_separator, 
                DEFAULT_CURRENCY.currency_position, 
                DEFAULT_CURRENCY.currency_symbol
            );
        };
    
        window.currencyFormat = currencyFormat;
    
        const clearCacheConfigUrl = '{{ route('backend.config_clear') }}';
    
        fetch(clearCacheConfigUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => console.log(data.message))
        .catch(error => console.error('Error:', error));
    </script>
</body>

</html>
