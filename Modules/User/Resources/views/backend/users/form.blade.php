@extends('backend.layouts.app')
@section('title') {{__($module_action)}} {{__($module_title)}}@endsection

@section('content')
<div class="form-content">
    {{ html()->form('POST', isset($data) ? route('backend.users.update', $data->id) : route('backend.users.store'))->attribute('data-toggle', 'validator')->id('form-submit')->class('requires-validation')->attribute('novalidate', 'novalidate')->attribute('enctype', 'multipart/form-data')->open() }}
        @csrf

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
            </div>
            <a href="{{ route('backend.users.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div> 

        @if (isset($data))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ html()->label(__('messages.profile_image'), 'profile_image')->class('form-label') }}
                            <div class="btn-file-upload">
                                <div class="mb-2 d-flex justify-content-center align-items-center text-center">
                                    <img id="imagePreview" src="{{ $data->profile_image ?? default_user_avatar() }}"
                                        alt="Profile Image" class="img-thumbnail avatar-150 object-cover">
                                </div>
                                <div
                                    class="mb-2
                                        d-flex justify-content-center align-items-center text-center gap-3">

                                    <button type="button" class="btn btn-sm btn-primary mb-2"
                                        id="uploadButton">{{ __('messages.upload_image') }}</button>
                                    <button type="button" class="btn btn-sm btn-danger mb-2" id="removeButton"
                                        style="display: none;">{{ __('messages.remove_image') }}</button>
                                </div>
                            </div>
                            {{ html()->file('profile_image')->id('profileImageInput')->class('form-control d-none')->attribute('accept', 'image/*') }}
                            @error('profile_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                    </div>
                    {{ html()->hidden('file_url')->id('file_url_image')->value(old('file_url', isset($data) ? $data->file_url : '')) }}
                    {{ html()->hidden('remove_image')->id('remove_image_flag')->value(0) }}
                    <div class="col-md-8 col-lg-8">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">{{ __('messages.lbl_first_name') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                    value="{{ old('first_name', $data->first_name ?? '') }}" name="first_name" id="first_name"
                                    placeholder="{{ __('messages.placeholder_first_name') }}"required>
                                <div class="help-block with-errors text-danger"></div>
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="name-error">{{ __('messages.first_name_required') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">{{ __('messages.lbl_last_name') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control"
                                    value="{{ old('last_name', $data->last_name ?? '') }}" name="last_name" id="last_name"
                                    placeholder="{{ __('messages.placeholder_last_name') }}" required>
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="name-error">{{ __('messages.last_name_required') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">{{ __('messages.lbl_username') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" id="username" 
                                    value="{{ old('username',$data->username ?? '') }}" placeholder="{{ __('messages.placeholder_username') }}" required>
                                @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                                <div class="invalid-feedback">{{ __('messages.username_field_required') }}</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('messages.lbl_email') }}<span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" value="{{ old('email', $data->email ?? '') }}"
                                    name="email" id="email" placeholder="{{ __('messages.placeholder_email') }}"
                                    required>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="mobile" class="form-label">{{ __('messages.lbl_contact_number') }}<span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control" value="{{ old('mobile', $data->mobile ?? '') }}"
                                    name="mobile" id="mobile" placeholder="{{ __('messages.lbl_conatct_number') }}"
                                    required>
                                @error('mobile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="mobile-error">{{ __('messages.contact_number_required') }}</div>
                            </div>

                            @if (!isset($data->id))
                            <div class="col-md-6">
                                <label for="password" class="form-label">{{ __('messages.lbl_password') }}<span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control"
                                    value="{{ old('password', $data->password ?? '') }}" name="password" id="password"
                                    placeholder="{{ __('messages.placeholder_password') }}" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('password')">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="password-error">{{ __('messages.password_field_required') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation"
                                    class="form-label">{{ __('messages.lbl_confirm_password') }}<span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control"
                                    value="{{ old('password_confirmation', $data->password_confirmation ?? '') }}"
                                    name="password_confirmation" id="password_confirmation"
                                    placeholder="{{ __('messages.placeholder_confirm_password') }}" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('password_confirmation')">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                @error('password_confirmation')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="password_confirmation-error">{{ __('messages.confirm_password_required') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label">{{ __('messages.lbl_gender') }}</label><span class="text-danger">*</span>
                        <div class="d-flex align-items-center flex-sm-nowrap flex-wrap gap-3">
                            <label class="form-check form-control px-5 cursor-pointer">
                                <div>
                                    <input class="form-check-input" type="radio" name="gender" id="male"
                                        value="male"
                                        {{ old('gender', isset($data) ? $data->gender : 'male') == 'male' ? 'checked' : '' }}>
                                    <span class="form-check-label">{{ __('messages.lbl_male') }}</span>
                                </div>
                            </label>
                            <label class="form-check form-control px-5 cursor-pointer">
                                <div>
                                    <input class="form-check-input" type="radio" name="gender" id="female"
                                        value="female"
                                        {{ old('gender', isset($data) ? $data->gender : 'male') == 'female' ? 'checked' : '' }}>
                                    <span class="form-check-label">{{ __('messages.lbl_female') }}</span>
                                </div>
                            </label>
                            <label class="form-check form-control px-5 cursor-pointer">
                                <div>
                                    <input class="form-check-input" type="radio" name="gender" id="other"
                                        value="other"
                                        {{ old('gender', isset($data) ? $data->gender : 'male') == 'other' ? 'checked' : '' }}>
                                    <span class="form-check-label">{{ __('messages.lbl_other') }}</span>
                                </div>
                            </label>
                        </div>

                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{ __('messages.gender_required') }}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="user_type" class="form-label">{{ __('messages.lbl_user_type') }}<span class="text-danger">*</span></label>
                        <select class="form-select select2" name="user_type" id="user_type" required>
                            <option value="" >{{ __('messages.select_user_type') }}</option>
                            <option value="user" {{ old('user_type', $data->user_type ?? '') == 'user' ? 'selected' : '' }}>
                                {{ __('messages.lbl_customer') }}
                            </option>
                            @if (multivendor() == 1)
                            <option value="vendor" {{ old('user_type', $data->user_type ?? '') == 'vendor' ? 'selected' : '' }}>
                                {{ __('messages.lbl_vendor') }}
                            </option>
                            @endif
                            <option value="collector" {{ old('user_type', $data->user_type ?? '') == 'collector' ? 'selected' : '' }}>
                                {{ __('messages.lbl_collector') }}
                            </option>
                        </select>
                        @error('user_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="user_type-error">{{ __('messages.user_type_required') }}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="date_of_birth" class="form-label">{{ __('messages.lbl_date_of_birth') }} <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control datetimepicker"
                            value="{{ old('date_of_birth', isset($data) ? $data->date_of_birth : '') }}" name="date_of_birth"
                            id="date_of_birth" max="{{ date('Y-m-d') }}"
                            placeholder="{{ __('messages.placeholder_date_of_birth') }}" required>
                        @error('date_of_birth')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="date_of_birth-error">{{ __('messages.date_of_birth_required') }}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="status" class="form-label"> {{ __('messages.lbl_status') }}</label>
                        <div class="d-flex align-items-center justify-content-between form-control">
                            <label for="status" class="form-label mb-0 text-body"> {{ __('messages.active') }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="status" value="0"> <!-- Hidden input field -->
                                <input class="form-check-input" type="checkbox" id="status" name="status"
                                    value="1" {{ old('status', $data->status ?? 1) == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="address" class="form-label">{{ __('messages.lbl_address') }}</label>
                        <textarea class="form-control" name="address" id="address" rows="3"
                            placeholder="{{ __('messages.placeholder_address') }}">{{ old('address', $data->address ?? '') }}</textarea>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="country" class="form-label">{{ __('messages.country') }}<span class="text-danger">*</span></label>
                        <select name="country_id" class="form-select select2" id="country" required>
                            <option value="" disabled selected>{{ __('messages.select_country') }}</option>
                            @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', isset($data) ? $data->country_id : '') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.country_required') }}</div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="state" class="form-label">{{ __('messages.state') }}<span class="text-danger">*</span></label>
                        <select name="state_id" class="form-select select2" id="state" required>
                            <option value="" disabled selected>{{ __('messages.select_state') }}</option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id', isset($data) ? $data->state_id : '') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('state_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.state_required') }}</div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="city" class="form-label">{{ __('messages.city') }}<span class="text-danger">*</span></label>
                        <select name="city_id" class="form-select select2" id="city" required>
                            <option value="" disabled selected>{{ __('messages.select_city') }}</option>
                            @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', isset($data) ? $data->city_id : '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.city_required') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end">
            <button type="submit" class="btn btn-primary" >{{ __('messages.save') }}</button>
        </div>
    </form>
</div>

 
@endsection
@push('after-scripts')
    <script>
        document.getElementById('mobile').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#date_of_birth", {
            dateFormat: "Y-m-d", // Format as YYYY-MM-DD
            maxDate: "today", // Disable future dates
        });
    });


            // Elements
            const $uploadButton = $('#uploadButton');
const $removeButton = $('#removeButton');
const $profileImageInput = $('#profileImageInput');
const $imagePreview = $('#imagePreview');

// Upload Button Click
$uploadButton.on('click', function() {
    $profileImageInput.trigger('click');
});

// Image Preview
$profileImageInput.on('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $imagePreview.attr('src', e.target.result);
            $imagePreview.show();
            $removeButton.show();
        }
        reader.readAsDataURL(file);
    }
});

// Remove Button Click
$removeButton.on('click', function() {
    $profileImageInput.val('');
    $imagePreview.attr('src', '{{ default_user_avatar() }}');
    $(this).hide();
});


    // Helper functions for error handling
    function showFieldError(fieldId, message) {
        removeFieldError(fieldId);
        const errorDiv = `<div class="text-danger field-error-${fieldId}">${message}</div>`;
        $(`#${fieldId}`).addClass('is-invalid').after(errorDiv);
    }

    function removeFieldError(fieldId) {
        $(`#${fieldId}`).removeClass('is-invalid');
        $(`.field-error-${fieldId}`).remove();
    }

    // Prevent form submission if there are errors
    

    
function validateEmail(emailInput) {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const userId = '{{ isset($data) ? $data->id : "" }}';
    const originalEmail = '{{ isset($data) ? $data->email : "" }}';
    let isValid = true;
    
    // Reset validation state and remove any existing error messages
    removeFieldError('email');
    emailInput.classList.remove('is-invalid', 'is-valid');
    
    // Check if empty
    if (!emailInput.value) {
        showFieldError('email', "{{ __('messages.email_field_is_required') }}");
        isValid = false;
        return false;
    }
    
    // Check format
    if (!emailRegex.test(emailInput.value)) {
        showFieldError('email', "{{ __('messages.please_enter_a_valid_email_format') }}");
        isValid = false;
        return false;
    }


    
    return isValid;
}

// Helper functions for error handling
function showFieldError(fieldId, message) {
    removeFieldError(fieldId);
    const errorDiv = `<div class="text-danger field-error-${fieldId}">${message}</div>`;
    $(`#${fieldId}`).addClass('is-invalid').after(errorDiv);
}

function removeFieldError(fieldId) {
    $(`#${fieldId}`).removeClass('is-invalid');
    $(`.field-error-${fieldId}`).remove();
}

// Form submit handler
const form = document.getElementById('form-submit');
form.addEventListener('submit', function(e) {
    const emailInput = document.getElementById('email');
    if (!validateEmail(emailInput)) {
        e.preventDefault();
        emailInput.focus();
    }
});

// Email blur event listener
document.getElementById('email').addEventListener('blur', function() {
    validateEmail(this);
});
    </script>
@endpush
