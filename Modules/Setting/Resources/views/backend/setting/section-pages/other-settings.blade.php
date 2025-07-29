@extends('setting::backend.setting.index')

@section('settings-content')
<form method="POST" action="{{ route('backend.setting.store') }}" enctype="multipart/form-data">
    @csrf

    <div>
        <h4 class="mb-4"><i class="ph ph-squares-four"></i> {{ __('messages.app_configuration') }} </h4>
    </div>


    

    <div class="form-group border-bottom pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0" for="category-enable_google_login">{{ __('messages.lbl_enable_google_login') }}</label>
            <input type="hidden" value="0" name="google_login">
            <div class="form-check form-switch m-0">
                {{ html()->checkbox('google_login', old('google_login', $settings['google_login'] ?? 0) == 1, 1)
                    ->class('form-check-input')
                    ->id('category-enable_google_login') }}
            </div>
        </div>
    </div>

    <div class="form-group border-bottom pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0" for="category-enable_apple_login">{{ __('messages.lbl_enable_apple_login') }}</label>
            <input type="hidden" value="0" name="apple_login">
            <div class="form-check form-switch m-0">
                {{ html()->checkbox('apple_login', old('apple_login', $settings['apple_login'] ?? 0) == 1, 1)
                    ->class('form-check-input')
                    ->id('category-enable_apple_login') }}
            </div>
        </div>
    </div>

    <div class="form-group border-bottom pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0" for="category-enable_otp_login">{{ __('messages.lbl_enable_otp_login') }}</label>
            <input type="hidden" value="0" name="otp_login">
            <div class="form-check form-switch m-0">
                {{ html()->checkbox('otp_login', old('otp_login', $settings['otp_login'] ?? 0) == 1, 1)
                    ->class('form-check-input')
                    ->id('category-enable_otp_login') }}
            </div>
        </div>
    </div>

    <div class="form-group border-bottom pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0" for="category-enable_user_push_notification">{{ __('messages.lbl_enable_user_push_notification') }}</label>
            <input type="hidden" value="0" name="is_user_push_notification">
            <div class="form-check form-switch m-0">
                {{ html()->checkbox('is_user_push_notification', old('is_user_push_notification', $settings['is_user_push_notification'] ?? 0) == 1, 1)
                    ->class('form-check-input')
                    ->id('category-enable_user_push_notification') }}
            </div>
        </div>
    </div>

    <div class="form-group border-bottom pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0" for="category-enable_vendor_push_notification">{{ __('messages.lbl_enable_vendor_push_notification') }}</label>
            <input type="hidden" value="0" name="is_vendor_push_notification">
            <div class="form-check form-switch m-0">
                {{ html()->checkbox('is_vendor_push_notification', old('is_vendor_push_notification', $settings['is_vendor_push_notification'] ?? 0) == 1, 1)
                    ->class('form-check-input')
                    ->id('category-enable_vendor_push_notification') }}
            </div>
        </div>
    </div>

   
    <div class="form-group border-bottom pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0" for="is_multi_vendor">{{ __('messages.lbl_multi_vendor') }}</label>
            <input type="hidden" value="0" name="is_multi_vendor">
            <div class="form-check form-switch m-0">
                {{ html()->checkbox('is_multi_vendor', old('is_multi_vendor', $settings['is_multi_vendor'] ?? 0) == 1, 1)
                    ->class('form-check-input')
                    ->id('is_multi_vendor') }}
            </div>
        </div>
    </div>
    <div class="form-group border-bottom pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label m-0" for="category-firebase_notification">{{ __('messages.lbl_firebase_notification') }}</label>

            <input type="hidden" value="0" name="is_firebase_notification">
            <div class="form-check form-switch m-0">
                {{ html()->checkbox('is_firebase_notification', old('is_firebase_notification', $settings['is_firebase_notification'] ?? 0) == 1, 1)
                    ->class('form-check-input')
                    ->id('category-firebase_notification')
                    ->attribute('onclick', 'toggleFirebaseKeyField()') }}
            </div>
        </div>
    </div>

    <div id="firebase-key-field" style="display: {{ old('firebase_notification', $settings['firebase_notification'] ?? 0) == 1 ? 'block' : 'none' }};">
        <div class="row">
            <div class="form-group col-sm-6">
                <label for="category-firebase_key" class="form-label">{{ __('messages.lbl_firebase_key') }}</label>
                {{ html()->text('firebase_key', old('firebase_key', $settings['firebase_key'] ?? ''))
                    ->class('form-control')
                    ->id('firebase_key')
                    }}
                @error('firebase_key')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group col-sm-6 mb-0 ">
                <label for="json_file" class="form-label">
        
                    {{ trans('messages.json_file') }} <span class="ml-3"><a class="text-primary" href="https://console.firebase.google.com/">Download JSON File</a></span>
                </label>
               
                <div class="custom-file">
                   
                    <input type="file" class="form-control" name="json_file" id="json_file" accept="application/json">
                    <label id="additionalFileHelp" class="custom-file-label upload-label border-0"> {{ $settings['json_file'] ? $settings['json_file'] : 'Upload Firebase JSON file only once.' }}</label>
                    <small class="help-block with-errors text-danger"></small>
                </div>
            </div>
        </div>
    </div>

    <div class="border-bottom pb-3">
    <div class="form-group col-md-12 d-flex justify-content-between mt-3">
        <label for="whatsapp_notification" class="mb-0 form-label">{{ __('messages.whatsapp_notificaion') }}</label>
        <div class="form-check form-switch m-0">
            <input type="hidden" value="0" name="whatsapp_notification">
            <input type="checkbox" class="form-check-input" name="whatsapp_notification" id="whatsapp_notification" value="1" 
                {{ isset($settings['whatsapp_notification']) && $settings['whatsapp_notification'] == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="whatsapp_notification"></label>
        </div>
    </div>
    <div id="whatsapp-settings" style="display: {{ isset($settings['whatsapp_notification']) && $settings['whatsapp_notification'] == 1 ? 'block' : 'none' }};">
        <!-- WhatsApp Configuration -->
        <div class="row mt-3">
            <div class="form-group col-sm-6">
                <label for="twilio_sid_whatsapp" class="col-sm-6 form-label">{{ __('messages.twilio_sid') }} (WhatsApp)</label>
                    {{ html()->text('twilio_sid_whatsapp', $settings['twilio_sid_whatsapp'] ?? '')
                        ->class('form-control')
                        ->id('twilio_sid_whatsapp')
                        ->placeholder(__('messages.twilio_sid')) }}
            </div>

            <div class="form-group col-sm-6">
                <label for="twilio_auth_token_whatsapp" class="col-sm-6 form-label">{{ __('messages.twilio_auth_token') }} (WhatsApp)</label>
                    {{ html()->text('twilio_auth_token_whatsapp', $settings['twilio_auth_token_whatsapp'] ?? '')
                        ->class('form-control')
                        ->id('twilio_auth_token_whatsapp')
                        ->placeholder(__('messages.twilio_auth_token')) }}
            </div>

            <div class="form-group col-sm-6">
                <label for="twilio_whatsapp_number" class="col-sm-6 form-label">{{ __('messages.twilio_whatsapp_number') }} (WhatsApp)</label>
                    {{ html()->text('twilio_whatsapp_number', $settings['twilio_whatsapp_number'] ?? '')
                        ->class('form-control')
                        ->id('twilio_whatsapp_number')
                        ->placeholder(__('messages.twilio_whatsapp_number')) }}
            </div>
        </div>
    </div>
</div>

<div class="border-bottom pb-3 mb-3">
    <div class="form-group col-md-12 d-flex justify-content-between mt-3">
        <label for="sms_notification" class="mb-0 form-label">{{ __('messages.sms_notificaion') }}</label>
        <div class="form-check form-switch m-0">
            <input type="hidden" value="0" name="sms_notification">
            <input type="checkbox" class="form-check-input" name="sms_notification" id="sms_notification" value="1"
                {{  isset($settings['sms_notification']) && $settings['sms_notification'] == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="sms_notification"></label>
        </div>
    </div>
 
    <div id="sms-settings" style="display: {{ isset($settings['sms_notification']) && $settings['sms_notification'] == 1 ? 'block' : 'none' }};">
        <div class="row mt-3">
            <!-- SMS Configuration -->
            <div class="form-group col-sm-6">
                <label for="twilio_sid_sms" class="col-sm-6 form-label">{{ __('messages.twilio_sid') }} (SMS)</label>
                    {{ html()->text('twilio_sid_sms', $settings['twilio_sid_sms'] ?? '')
                        ->class('form-control')
                        ->id('twilio_sid_sms')
                        ->placeholder(__('messages.twilio_sid')) }}
            </div>
    
            <div class="form-group col-sm-6">
                <label for="twilio_auth_token_sms" class="col-sm-6 form-label">{{ __('messages.twilio_auth_token') }} (SMS)</label>   
                    {{ html()->text('twilio_auth_token_sms', $settings['twilio_auth_token_sms'] ?? '')
                        ->class('form-control')
                        ->id('twilio_auth_token_sms')
                        ->placeholder(__('messages.twilio_auth_token')) }}
            </div>
    
            <div class="form-group col-sm-6">
                <label for="twilio_phone_number_sms" class="col-sm-6 form-label">{{ __('messages.twilio_phone_number') }} (SMS)</label>
                    {{ html()->text('twilio_phone_number_sms', $settings['twilio_phone_number_sms'] ?? '')
                        ->class('form-control')
                        ->id('twilio_phone_number_sms')
                        ->placeholder(__('messages.twilio_phone_number')) }}
            </div>  
        </div>
    </div>
</div>
    

    <div class="d-flex justify-content-end">
        <button type="submit" id="submit-button" class="btn btn-primary">{{ __('messages.save') }}</button>
    </div>
</form>
@endsection

@push('after-scripts')
<script>

    function toggleFirebaseKeyField() {
    const firebaseNotificationEnabled = document.getElementById('category-firebase_notification').checked;
    document.getElementById('firebase-key-field').style.display = firebaseNotificationEnabled ? 'block' : 'none';

    const input = document.getElementById('firebase_key');
    if (firebaseNotificationEnabled) {
        input.disabled = false;
    } else {
        input.disabled = true;
    }
}

// Add this to the existing script section

function toggleWhatsAppSettings() {
    const whatsappEnabled = document.getElementById('whatsapp_notification').checked;
    document.getElementById('whatsapp-settings').style.display = whatsappEnabled ? 'block' : 'none';

    // Toggle required attribute for WhatsApp fields
    const whatsappInputs = [
        'twilio_sid_whatsapp',
        'twilio_auth_token_whatsapp',
        'twilio_whatsapp_number'
    ];

    whatsappInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (whatsappEnabled) {
            input.setAttribute('required', 'required');
        } else {
            input.removeAttribute('required');
        }
    });
}

function toggleSMSSettings() {
    const smsEnabled = document.getElementById('sms_notification').checked;
    document.getElementById('sms-settings').style.display = smsEnabled ? 'block' : 'none';

    // Toggle required attribute for SMS fields
    const smsInputs = [
        'twilio_sid_sms',
        'twilio_auth_token_sms',
        'twilio_phone_number_sms'
    ];

    smsInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (smsEnabled) {
            input.setAttribute('required', 'required');
        } else {
            input.removeAttribute('required');
        }
    });
}



// Add event listeners to the checkboxes
document.getElementById('whatsapp_notification').addEventListener('change', toggleWhatsAppSettings);
document.getElementById('sms_notification').addEventListener('change', toggleSMSSettings);

    // Initialize the state of the fields when the page loads
    document.addEventListener('DOMContentLoaded', function() {

        toggleFirebaseKeyField();
        toggleWhatsAppSettings();
        toggleSMSSettings();
    });

</script>
@endpush
