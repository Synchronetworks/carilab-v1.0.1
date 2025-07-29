@extends('backend.layouts.app')
@section('title')
    {{ __($module_title) }}
@endsection
@section('content')
    <div class="form-content">
        <form action="{{ route('backend.collectors.store') }}" method="POST" enctype="multipart/form-data"
            class='requires-validation' id="form-submit" novalidate>
            @csrf
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="card-input-title">
                    <h4 class="">{{ __('messages.basic_information') }}</h4>
                </div>
                <a href="{{ route('backend.collectors.index') }}" class="btn btn-sm btn-primary">
                    <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.profile_image') }}</label>
                                <div class="btn-file-upload">
                                    <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                        <img id="imagePreview" src="{{ $data->profile_image ?? default_user_avatar() }}"
                                            alt="Profile Image" class="img-thumbnail avatar-150 object-cover">
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center text-center gap-3">
                                        <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                                            {{ __('messages.upload_image') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" id="removeButton"
                                            style="display: none;">
                                            {{ __('messages.remove_image') }}
                                        </button>
                                    </div>
                                </div>
                                <input type="file" name="profile_image" id="profileImageInput"
                                    class="form-control d-none" accept="image/*">
                                @error('profile_image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-md-8">
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">{{ __('messages.lbl_first_name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        placeholder="{{ __('messages.placeholder_first_name') }}"
                                        value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.first_name_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">{{ __('messages.lbl_last_name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        placeholder="{{ __('messages.placeholder_last_name') }}"
                                        value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.last_name_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ __('messages.lbl_email') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="{{ __('messages.placeholder_email') }}" value="{{ old('email') }}"
                                        required>
                                    <div class="invalid-feedback">{{ __('messages.email_required') }}</div>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="username" class="form-label">{{ __('messages.lbl_username') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username" id="username"
                                        placeholder="{{ __('messages.placeholder_username') }}"
                                        value="{{ old('username') }}" required>
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.username_field_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label">{{ __('messages.lbl_password') }}<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password"
                                            placeholder="{{ __('messages.placeholder_password') }}" required>
                                        <span class="input-group-text cursor-pointer"
                                            onclick="togglePasswordVisibility('password')">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                        <div class="invalid-feedback">{{ __('messages.password_field_required') }}</div>
                                    </div>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label">{{ __('messages.lbl_confirm_password') }}<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password_confirmation"
                                            placeholder="{{ __('messages.placeholder_confirm_password') }}"
                                            id="password_confirmation" required>
                                        <span class="input-group-text cursor-pointer"
                                            onclick="togglePasswordVisibility('password_confirmation')">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                        <div class="invalid-feedback">{{ __('messages.confirm_password_required') }}</div>
                                    </div>
                                    @error('password_confirmation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if (Setting('collector_commission_type') == 'per_collector')
                                    <div class="col-md-6">
                                        <label for="commission_type"
                                            class="form-label">{{ __('messages.commission_type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="commission_type" id="commission_type" class="form-select select2">
                                            <option value="fixed" selected {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed') }}</option>
                                            <option value="percentage"  {{ old('commission_type') == 'percentage' ? 'selected' : '' }}>{{ __('messages.percentage') }}</option>
                                        </select>
                                        @error('commission_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <div class="invalid-feedback">{{ __('messages.commission_type_required') }}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="commission" class="form-label">{{ __('messages.lbl_commission') }}
                                            <span id="commission_symbol"></span> <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="commission" min="0"
                                            step="any" id="commission" value="{{ old('commission') }}"
                                            oninput="this.value = (this.value > 100 && $('#commission_type').val() === 'percentage') ? 100 : this.value">
                                        >
                                        @error('commission')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <div class="invalid-feedback">{{ __('messages.commission_field_required') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="mobile" class="form-label">{{ __('messages.lbl_contact_number') }}<span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control" value="{{ old('mobile') }}" name="mobile"
                                id="mobile" placeholder="{{ __('messages.placeholder_contact_number') }}" required>
                            <div class="invalid-feedback" id="mobile-error">{{ __('messages.phone_required') }}</div>
                            @error('mobile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="date_of_birth" class="form-label">{{ __('messages.lbl_date_of_birth') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control datetimepicker" value="{{ old('date_of_birth') }}"
                                name="date_of_birth" id="date_of_birth" max="{{ date('Y-m-d') }}"
                                placeholder="{{ __('messages.placeholder_date_of_birth') }}" required>
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback" id="date_of_birth-error">
                                {{ __('messages.date_birth_required') }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">{{ __('messages.lbl_gender') }}<span
                                    class="text-danger">*</span></label>
                            <div class="d-flex align-items-center flex-sm-nowrap flex-wrap gap-3">
                                <label class="form-check form-control px-5 cursor-pointer">
                                    <div>
                                        <input class="form-check-input" type="radio" name="gender" id="male"
                                            value="male" {{ old('gender', 'male') == 'male' ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ __('messages.lbl_male') }}</span>
                                    </div>
                                </label>
                                <label class="form-check form-control px-5 cursor-pointer">
                                    <div>
                                        <input class="form-check-input" type="radio" name="gender" id="female"
                                            value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ __('messages.lbl_female') }}</span>
                                    </div>
                                </label>
                                <label class="form-check form-control px-5 cursor-pointer">
                                    <div>
                                        <input class="form-check-input" type="radio" name="gender" id="other"
                                            value="other" {{ old('gender') == 'other' ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ __('messages.lbl_other') }}</span>
                                    </div>
                                </label>
                            </div>
                            @error('gender')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-input-title mb-3">
                <h4 class="m-0">
                    {{ multivendor() == 1 ? __('messages.vendor_lab_information') : __('messages.lbl_lab_name') }}
                </h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-sm-12">
                            <div class="row gy-3 mb-4">
                                @php
                                    $isVendor = auth()->user()->user_type === 'vendor';
                                @endphp

                                @if (!$isVendor && multivendor() == 1)
                                    <div class="col-md-4 mb-3">
                                        <label for="vendor_id" class="form-label">{{ __('messages.select_vendor') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="vendor_id" id="vendor_id" class="form-select select2" required>
                                            <option value="" disabled selected>{{ __('messages.select_vendor') }}
                                            </option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">
                                                    {{ $vendor->first_name }} {{ $vendor->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <div class="invalid-feedback">{{ __('messages.vendor_required') }}</div>
                                    </div>
                                @else
                                    {{-- Hidden input for vendor users --}}
                                    <input type="hidden" name="vendor_id" value="{{ auth()->id() }}">
                                @endif

                                <div class="col-md-6 col-lg-4">
                                    <label for="lab_id" class="form-label">{{ __('messages.select_lab') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="lab_id" id="lab_id" class="form-select select2" required>
                                        <option value="" disabled selected>{{ __('messages.select_lab') }}</option>

                                    </select>
                                    @error('lab_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.lab_required') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-input-title mb-3">
                <h4 class="m-0">{{ __('messages.other_information') }}</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6 col-lg-4">
                            <label for="education" class="form-label">{{ __('messages.education') }}</label>
                            <input type="text" class="form-control" name="education" id="education"
                                placeholder="{{ __('messages.placeholder_education') }}" value="{{ old('education') }}">
                            @error('education')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.education_required') }}</div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="degree" class="form-label">{{ __('messages.degree') }}</label>
                            <input type="text" class="form-control" name="degree" id="degree"
                                placeholder="{{ __('messages.placeholder_degree') }}" value="{{ old('degree') }}">
                            @error('degree')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.degree_required') }}</div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="bio" class="form-label">{{ __('messages.bio') }}</label>
                            <input type="text" class="form-control" name="bio" id="bio"
                                placeholder="{{ __('messages.placeholder_bio') }}" value="{{ old('bio') }}">
                            @error('bio')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.bio_required') }}</div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="experience" class="form-label">{{ __('messages.experience') }}</label>
                            <input type="text" class="form-control" name="experience"
                                placeholder="{{ __('messages.placeholder_experience') }}" id="experience"
                                value="{{ old('experience') }}">
                            @error('experience')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.experience_required') }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="status" class="form-label">{{ __('messages.lbl_status') }}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="status"
                                    class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        value="1" {{ old('status', 1) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="address" class="form-label">{{ __('messages.lbl_address') }}</label>
                            <textarea class="form-control" name="address" id="address"
                                placeholder="{{ __('messages.placeholder_address') }}" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="country" class="form-label">{{ __('messages.country') }}<span
                                    class="text-danger">*</span></label>
                            <select name="country_id" class="form-select select2" id="country" required>
                                <option value="" disabled selected>{{ __('messages.select_country') }}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.country_required') }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="state" class="form-label">{{ __('messages.state') }}<span
                                    class="text-danger">*</span></label>
                            <select name="state_id" class="form-select select2" id="state" required>
                                <option value="" disabled selected>{{ __('messages.select_state') }}</option>
                            </select>
                            @error('state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.state_required') }}</div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="city" class="form-label">{{ __('messages.city') }}<span
                                    class="text-danger">*</span></label>
                            <select name="city_id" class="form-select select2" id="city" required>
                                <option value="" disabled selected>{{ __('messages.select_city') }}</option>
                            </select>
                            @error('city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.city_required') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="submit" id='submit-button' class="btn btn-primary">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('after-scripts')
    <script>
        document.getElementById('mobile').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#date_of_birth", {
                dateFormat: "Y-m-d", 
                maxDate: "today",
            });
        });


        function validateNameInput(inputId, errorId) {
            document.getElementById(inputId).addEventListener('input', function(e) {
                let regex = /^[A-Za-z\s]*$/; 
                let inputValue = this.value;
                if (!regex.test(inputValue)) {
                    this.value = inputValue.replace(/[^A-Za-z\s]/g, '');
                    document.getElementById(errorId).classList.remove('d-none'); 
                } else {
                    document.getElementById(errorId).classList.add('d-none'); 
                }
            });
        }

        // Apply validation to First Name and Last Name
        validateNameInput('first_name', 'firstNameError');
        validateNameInput('last_name', 'lastNameError');
    
        const $uploadButton = $('#uploadButton');
        const $removeButton = $('#removeButton');
        const $profileImageInput = $('#profileImageInput');
        const $imagePreview = $('#imagePreview');

        $uploadButton.on('click', function() {
            $profileImageInput.trigger('click');
        });

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

        $removeButton.on('click', function() {
            $profileImageInput.val('');
            $imagePreview.attr('src', '{{ asset('images/default-avatar.png') }}');
            $(this).hide();
        });

      
        document.addEventListener('DOMContentLoaded', function() {

            //country wise state get
            const countrySelect = document.getElementById('country');
            if (countrySelect) {
                $(countrySelect).select2().on('change', function() {
                    const countryId = this.value;
                    const url = `{{ route('backend.state.index_list') }}?country_id=${countryId}`;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            const stateSelect = document.getElementById('state');
                            stateSelect.innerHTML =
                                '<option value="" disabled selected>{{ __('messages.select_state') }}</option>';
                            data.forEach(state => {
                                stateSelect.innerHTML +=
                                    `<option value="${state.id}">${state.name}</option>`;
                            });
                            document.getElementById('city').innerHTML =
                                '<option value="" disabled selected>{{ __('messages.select_city') }}</option>'; // Clear city dropdown
                        });
                });
            } else {
                console.error("{{ __('messages.error') }}");
            }


            //state wise city get

            const stateSelect = document.getElementById('state');
            if (stateSelect) {
                $(stateSelect).select2().on('change', function() {
                    const stateId = this.value;
                    const url = `{{ route('backend.city.index_list') }}?state_id=${stateId}`;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            const citySelect = document.getElementById('city');
                            citySelect.innerHTML =
                                '<option value="" disabled selected>{{ __('messages.select_city') }}</option>';
                            data.forEach(city => {
                                citySelect.innerHTML +=
                                    `<option value="${city.id}">${city.name}</option>`;
                            });
                        });
                });
            } else {
                console.error("{{ __('messages.error') }}");
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-submit');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');

            confirmPasswordField.addEventListener('input', function() {
                if (passwordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.setCustomValidity("{{ __('messages.passwords_do_not_match') }}");
                    confirmPasswordField.classList.add('is-invalid');
                } else {
                    confirmPasswordField.setCustomValidity('');
                    confirmPasswordField.classList.remove('is-invalid');
                }
            });

            form.addEventListener('submit', function(e) {
                if (passwordField.value !== confirmPasswordField.value) {
                    e.preventDefault();
                    confirmPasswordField.setCustomValidity("{{ __('messages.passwords_do_not_match') }}");
                    confirmPasswordField.classList.add('is-invalid');
                }
            });
        });

        $(document).ready(function() {
            $('#tax_id').select2({
                placeholder: 'Select Taxes',
                allowClear: true,
                width: '100%'
            });
        });

        let loggedInVendorId = "{{ auth()->user()->user_type == 'vendor' ? auth()->id() : null }}";

        function loadLab(vendor_id = null) {
            $.ajax({
                url: "{{ route('backend.labs.index_list') }}",
                type: "GET",
                data: {
                    vendor_id: vendor_id
                },
                success: function(data) {
                    $('#lab_id').empty();
                    $('#lab_id').append('<option value="">{{ __('messages.select_lab') }}</option>');
                    $.each(data, function(key, value) {
                        $('#lab_id').append('<option value="' + value.id + '">' + value.name +
                            '</option>');
                    });
                }
            });
        }
        if (loggedInVendorId) {
            loadLab(loggedInVendorId);
        } else {
            loadLab()
        }
      
        $('#vendor_id').on('change', function() {
            let vendor_id = $(this).val();

            if (vendor_id) {
               
                loadLab(vendor_id);
            } else {
              
                loadLab();
            }

        });
        
        $(document).ready(function() {
            function updateCommissionSymbol() {
                let commissionType = $('#commission_type').val();
                let symbolSpan = $('#commission_symbol');

                if (commissionType === "percentage") {
                    symbolSpan.text('(%)');
                } else if (commissionType === "fixed") {
                    symbolSpan.text("{{ getCurrencySymbol() }}"); 
                } else {
                    symbolSpan.text('');
                }
            }

        
            updateCommissionSymbol();

            $('#commission_type').on('change', updateCommissionSymbol);
        });

        document.getElementById('email').addEventListener('blur', function() {
            validateEmail(this);
        });

        function validateEmail(emailInput) {
            const errorDiv = emailInput.nextElementSibling;
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        
            emailInput.classList.remove('is-invalid', 'is-valid');
            var emailRequiredMessage = "{{ __('messages.email_required') }}";
            var emailvalidMessage = "{{ __('messages.email_valid') }}";
            if (!emailInput.value) {
                emailInput.classList.add('is-invalid');
                errorDiv.textContent = emailRequiredMessage;
                return false;
            }

            if (!emailRegex.test(emailInput.value)) {
                emailInput.classList.add('is-invalid');
                errorDiv.textContent = emailvalidMessage;
                return false;
            }

            emailInput.classList.add('is-valid');
            errorDiv.textContent = '';
            return true;
        }

     
        const form = document.getElementById('form-submit');
        form.addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            if (!validateEmail(emailInput)) {
                e.preventDefault();
                emailInput.focus();
            }
        });
    </script>
@endpush
