@extends('backend.layouts.app')
@section('title'){{__($module_title)}}@endsection

@section('content')
    <div class="form-content">         
        <form action="{{ route('backend.labs.store') }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
            @csrf           
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="card-input-title">
                    <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
                </div>
                <a href="{{ route('backend.labs.index') }}" class="btn btn-sm btn-primary">
                    <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row mb-4">                            
                        <div class="col-md-4">
                            <div class="form-group">
                                    <label class="form-label">{{__('messages.lab_logo')}}</label>
                                    <div class="btn-file-upload">
                                        <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                            <img id="imagePreview"
                                                src="{{ $data->profile_image ?? default_placeholder() }}"
                                                alt="placeholder Image" class="img-thumbnail avatar-150 object-cover">
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center text-center gap-3">
                                            <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                                                {{ __('messages.upload_image') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" id="removeButton" style="display: none;">
                                                {{ __('messages.remove_image') }}
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" name="logo" id="logoInput" class="form-control d-none" accept="image/png,image/jpeg">
                                    @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mt-4">
                                <label for="status" class="form-label">{{__('messages.lbl_status')}}</label>
                                <div class="d-flex align-items-center justify-content-between form-control">
                                    <label for="status" class="form-label mb-0 text-body">{{__('messages.active')}}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" 
                                            value="1" {{ old('status', 1) == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                <div class="invalid-feedback">{{__('messages.status_required')}}</div>
                            </div>
                        </div>

                        <div class="col-md-8 mt-3 mt-md-0">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{__('messages.lbl_lab_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" 
                                        value="{{ old('name') }}" placeholder="{{ __('messages.enter_lab_name') }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{__('messages.lab_name_required')}}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lab_code" class="form-label">{{__('messages.lab_code')}}</label>
                                    <input type="text" class="form-control" name="lab_code" id="lab_code" 
                                        value="{{ old('lab_code') }}" placeholder="{{ __('messages.enter_lab_code') }}">
                                    @error('lab_code') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{__('messages.lab_code_required')}}</div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">{{__('messages.lbl_description')}}</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" 
                                        placeholder="{{ __('messages.enter_description') }}">{{ old('description') }}</textarea>
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{__('messages.description_required')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>

            <!-- Vendor & Tax Information -->
            @if(multivendor() == 1)
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.vendor_tax_information')}}</h4>
            </div>
            @else
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.tax_information')}}</h4>
            </div>
            @endif
            <div class="card">
                <div class="card-body">                     
                    <div class="row gy-4">                        
                        @php
                            $isVendor = auth()->user()->user_type === 'vendor';
                        @endphp

                        @if(!$isVendor &&  multivendor()==1)
                        <div class="col-md-4">
                            <label for="vendor_id" class="form-label">{{__('messages.select_vendor')}}<span class="text-danger">*</span></label>
                            <select name="vendor_id" id="vendor_id" class="form-select select2" required>
                                <option value="" disabled selected>{{__('messages.select_vendor')}}</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->first_name }} {{ $vendor->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.vendor_required')}}</div>
                        </div>
                        @else
                        {{-- Hidden input for vendor users --}}
                        <input type="hidden" name="vendor_id" value="{{ auth()->id() }}">
                        @endif

                        <div class="col-md-4">
                            <label for="tax_id" class="form-label">{{__('messages.lbl_tax')}}</label>
                            <select name="tax_id[]" id="tax_id" class="form-select select2" multiple data-placeholder="{{ __('messages.select_tax') }}">
                                <option value="" disabled>{{ __('messages.select_tax') }}</option>
                            </select>
                            @if(multivendor() == 1)
                            <small class="text-muted">{{__('messages.default_tax_applied')}}</small>
                            @endif
                            @error('tax_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.tax_required')}}</div>
                        </div>

                        <div class="col-md-4">
                            <label for="tax_identification_number" class="form-label">{{__('messages.tax_identification_number')}}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tax_identification_number" id="tax_identification_number" 
                                value="{{ old('tax_identification_number') }}" placeholder="{{ __('messages.enter_tax_identification_number') }}" required>
                            @error('tax_identification_number') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.tax_identification_number_required')}}</div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Contact Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.other_information')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">                   
                    <div class="row gy-4">
                                           
                        <div class="col-md-4">
                            <label for="phone_number" class="form-label">{{__('messages.lbl_phone_number')}}<span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone_number" id="phone_number" 
                                value="{{ old('phone_number') }}" placeholder="{{ __('messages.enter_phone_number') }}" required>
                            @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback" id="phone_number-error">{{__('messages.phone_number_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">{{__('messages.email')}}<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" 
                                value="{{ old('email') }}" placeholder="{{ __('messages.enter_email') }}" required>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.email_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="time_slot" class="form-label">{{__('messages.time_slot')}}<span class="text-danger">*</span></label>
                            <select name="time_slot" id="time_slot" class="form-select select2" required>
                                <option value="" disabled selected>{{__('messages.select_time_slot')}}</option>
                                @foreach(range(5, 60, 5) as $minutes)
                                    <option value="{{ $minutes }}">{{ $minutes }} {{__('messages.minutes')}}</option>
                                @endforeach
                            </select>
                            @error('time_slot') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.time_slot_required')}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.address_information')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <label for="address_line_1" class="form-label">{{__('messages.address_line_1')}}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address_line_1" id="address_line_1" 
                                value="{{ old('address_line_1') }}" placeholder="{{ __('messages.enter_address_line_1') }}" required>
                            @error('address_line_1') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.address_line_1_required')}}</div>
                        </div>
                        <div class="col-md-6">
                            <label for="address_line_2" class="form-label">{{__('messages.address_line_2')}}</label>
                            <input type="text" class="form-control" name="address_line_2" id="address_line_2" 
                                value="{{ old('address_line_2') }}" placeholder="{{ __('messages.enter_address_line_2') }}">
                            @error('address_line_2') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.address_line_2_required')}}</div>
                        </div>
                       
                        <div class="col-md-3">
                            <label for="country" class="form-label">{{__('messages.country')}}<span class="text-danger">*</span></label>
                            <select name="country_id" class="form-select select2" id="country" required>
                                <option value="" disabled selected>{{__('messages.select_country')}}</option>
                                @foreach($countries as $id => $name)
                                    <option value="{{ $id }}" {{ old('country_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.country_required')}}</div>
                        </div>
                        <div class="col-md-3">
                            <label for="state" class="form-label">{{__('messages.state')}}<span class="text-danger">*</span></label>
                            <select name="state_id" class="form-select select2" id="state" required>
                                <option value="" disabled selected>{{__('messages.select_state')}}</option>
                            </select>
                            @error('state_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.state_required')}}</div>
                        </div>
                        <div class="col-md-3">
                            <label for="city" class="form-label">{{__('messages.city')}}<span class="text-danger">*</span></label>
                            <select name="city_id" class="form-select select2" id="city" required>
                                <option value="" disabled selected>{{__('messages.city')}}</option>
                            </select>
                            @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.city_required')}}</div>
                        </div>
                        <div class="col-md-3">
                            <label for="postal_code" class="form-label">{{__('messages.postal_code')}}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="postal_code" id="postal_code" 
                                value="{{ old('postal_code') }}" placeholder="{{ __('messages.enter_postal_code') }}" required>
                            @error('postal_code') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.postal_code_required')}}</div>
                        </div>
                        <!-- Hidden Geolocation Fields -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>
                </div>
            </div>

            <!-- License Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.license_information')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">                    
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <label for="license_number" class="form-label">{{__('messages.license_number')}}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="license_number" id="license_number" 
                                value="{{ old('license_number') }}" placeholder="{{ __('messages.enter_license_number') }}" required>
                            @error('license_number') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.license_number_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="license_document" class="form-label">{{__('messages.license_document')}}<span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="license_document" id="license_document" 
                                accept="application/pdf,image/jpeg" placeholder="{{ __('messages.upload_license_document') }}" required>
                            @error('license_document') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.license_document_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="license_expiry_date" class="form-label">{{__('messages.license_expiry_date')}}<span class="text-danger">*</span></label>
                            <input type="date" class="form-control datetimepicker" name="license_expiry_date" id="license_expiry_date" 
                                value="{{ old('license_expiry_date') }}" placeholder="{{ __('messages.enter_license_expiry_date') }}" required>
                            @error('license_expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.license_expiry_date_required')}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accreditation Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.accreditation_information')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <label for="accreditation_type" class="form-label">{{__('messages.accreditation_type')}}</label>
                            <select name="accreditation_type" id="accreditation_type" class="form-select select2">
                                <option value="" disabled selected>{{__('messages.select_accreditation_type')}}</option>
                                <option value="NABL" {{ old('accreditation_type') == 'NABL' ? 'selected' : '' }}>{{__('messages.nabl')}}</option>
                                <option value="ISO" {{ old('accreditation_type') == 'ISO' ? 'selected' : '' }}>{{__('messages.iso')}}</option>
                            </select>
                            @error('accreditation_type') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.accreditation_type_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="accreditation_certificate" class="form-label">{{__('messages.accreditation_certificate')}}</label>
                            <input type="file" class="form-control" name="accreditation_certificate" id="accreditation_certificate" 
                                accept="application/pdf,image/jpeg" placeholder="{{ __('messages.upload_accreditation_certificate') }}">
                            @error('accreditation_certificate') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.accreditation_certificate_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="accreditation_expiry_date" class="form-label">{{__('messages.accreditation_expiry_date')}}</label>
                            <input type="date" class="form-control datetimepicker" name="accreditation_expiry_date" id="accreditation_expiry_date" 
                                value="{{ old('accreditation_expiry_date') }}" placeholder="{{ __('messages.enter_accreditation_expiry_date') }}">
                            @error('accreditation_expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.accreditation_expiry_date_required')}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.payment_information')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">                    
                    <div class="row mb-4">                        
                        <div class="col-md-12">
                            <label class="form-label d-block">{{__('messages.payment_modes_accepted')}} <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="payment_modes[]" 
                                    id="payment_manual" value="manual" {{ in_array('manual', old('payment_modes', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_manual">{{__('messages.manual')}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="payment_modes[]" 
                                    id="payment_online" value="online" {{ in_array('online', old('payment_modes', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_online">{{__('messages.online')}}</label>
                            </div>
                            </div>
                             @error('payment_modes') <span class="text-danger">{{ $message }}</span> @enderror     
                             <div class="invalid-feedback">{{__('messages.payment_modes_rquired')}}</div>

                        <div class="col-md-12" id="online_payment_gateways" style="{{ in_array('online', old('payment_modes', [])) ? '' : 'display: none;' }}">
                            <label class="form-label d-block mt-2">{{__('messages.online_payment_gateways')}}</label>
                            <div class="gateway-options">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="select_all_gateways">
                                    <label class="form-check-label" for="select_all_gateways">
                                        {{ __('messages.all') }}
                                    </label>
                                </div>
                                @foreach($paymentGateways as $gateway)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input gateway-checkbox" type="checkbox" name="payment_gateways[]" 
                                            id="gateway_{{ $gateway->id }}" value="{{ $gateway->type }}"
                                            {{ in_array($gateway->id, old('payment_gateways', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gateway_{{ $gateway->id }}">
                                            @php
                                            $displayName = match($gateway->type) {
                                                'razorpayPayment' => __('messages.lbl_razorpay'),
                                                'stripePayment' => __('messages.lbl_stripe'),
                                                'paystackPayment' => __('messages.lbl_paystack'),
                                                'paypalPayment' => __('messages.lbl_paypal'),
                                                'flutterwavePayment' => __('messages.lbl_flutterwave'),
                                                default => ucfirst(str_replace(['Payment', '_payment'], '', $gateway->type))
                                            };
                                            @endphp
                                            {{ $displayName }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            @error('payment_gateways') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end">
                <button type="submit" class="btn btn-primary" id="submit-button">
                    {{__('messages.save')}}
                </button>
            </div>
        </form>
    </div>
@endsection


@push('after-scripts')
<link rel="stylesheet" href="{{ asset('css/flatpickr.css') }}">
<script src="{{ asset('js/flatpickr.js') }}"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#license_expiry_date", {
        dateFormat: "Y-m-d", // Format as YYYY-MM-DD
        minDate: "today", // Disable past dates
    });

    flatpickr("#accreditation_expiry_date", {
        dateFormat: "Y-m-d", // Format as YYYY-MM-DD
        minDate: "today", // Disable past dates
    });
});

    $(document).ready(function() {
        document.getElementById('phone_number').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $('#select_all_gateways').on('change', function() {
            $('.gateway-checkbox').prop('checked', this.checked);
            
            // Remove error message if any gateways are selected
            if (this.checked) {
                const errorMessage = document.querySelector('#gateway-error');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        });
   

        // Update select all checkbox state when individual checkboxes change
        $('.gateway-checkbox').on('change', function() {
            const allChecked = $('.gateway-checkbox:checked').length === $('.gateway-checkbox').length;
            $('#select_all_gateways').prop('checked', allChecked);
        })
        // Logo Image Handling
        const $uploadButton = $('#uploadButton');
const $removeButton = $('#removeButton');
const $logoInput = $('#logoInput');
const $imagePreview = $('#imagePreview');

$uploadButton.on('click', function() {
    $logoInput.trigger('click');
});

$logoInput.on('change', function() {
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
    $logoInput.val('');
    $imagePreview.attr('src', '{{ asset("images/default-logo.png") }}');
    $(this).hide();
});

        // Payment Mode Handling
        $('#payment_online').change(function() {
            $('#online_payment_gateways').toggle(this.checked);
        });

        // Initialize payment gateways div if online is checked
        if ($('#payment_online').is(':checked')) {
            $('#online_payment_gateways').show();
        }

        // Geolocation
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                },
                function(error) {
                    console.error('{{ __("messages.error_loading_location") }}', error);
                    
                }
            );
        } 

       
        $('#country').on('change', function() {
            var countryId = this.value;
            $("#state").html('');
            $.ajax({
                url: "{{route('backend.state.index_list')}}",
                type: "GET",
                data: {
                    country_id: countryId
                },
                dataType: 'json',
                success: function(result) {
                    $('#state').html('<option value="">{{ __("messages.select_state") }}</option>');
                    $.each(result, function(key, value) {
                        $("#state").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    $('#city').html('<option value="">{{ __("messages.select_city") }} </option>');
                }
            });
        });

        $('#state').on('change', function() {
            var stateId = this.value;
            $("#city").html('');
            $.ajax({
                url: "{{route('backend.city.index_list')}}",
                type: "GET",
                data: {
                    state_id: stateId
                },
                dataType: 'json',
                success: function(result) {
                    $('#city').html('<option value="">{{ __("messages.select_city") }}</option>');
                    $.each(result, function(key, value) {
                        $("#city").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        });

        // Get the form element
        const form = document.getElementById('form-submit');
        
        form.addEventListener('submit', function(event) {
            // Check if online payment is selected
            const onlinePaymentChecked = document.querySelector('input[name="payment_modes[]"][value="online"]').checked;
            validateEmail($('#email'))
           
           
            if (onlinePaymentChecked) {
                // Check if any payment gateway is selected
                const selectedGateways = document.querySelectorAll('input[name="payment_gateways[]"]:checked');
               
                  
                if (selectedGateways.length === 0 ) {
                    event.preventDefault();
                    // Add error message to the payment gateways section
                    const errorDiv = document.createElement('span');
                    errorDiv.className = 'text-danger d-block mt-2';
                    errorDiv.textContent = '{{ __("messages.pls_select_payment_gateway") }}';
                    
                    // Remove any existing error message first
                    const existingError = document.querySelector('#gateway-error');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Add the new error message
                    errorDiv.id = 'gateway-error';
                    document.getElementById('online_payment_gateways').appendChild(errorDiv);
                    return false;
                }
                
                else  if (hasValidationErrors) {
                        e.preventDefault();
                    
                        return false;
                    }
            }
        });

        // Toggle payment gateways section when online payment mode is checked/unchecked
        $('input[name="payment_modes[]"][value="online"]').on('change', function() {
            $('#online_payment_gateways').toggle(this.checked);
            
            // If unchecking online payment, uncheck all gateways and remove error message
            if (!this.checked) {
                $('input[name="payment_gateways[]"]').prop('checked', false);
                const errorMessage = document.querySelector('#gateway-error');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        });

        // Remove error message when a gateway is checked
        $('input[name="payment_gateways[]"]').on('change', function() {
            const errorMessage = document.querySelector('#gateway-error');
            if (errorMessage && document.querySelectorAll('input[name="payment_gateways[]"]:checked').length > 0) {
                errorMessage.remove();
            }
        });

        function loadTax(vendor_id = null) {
     $.ajax({
        url: "{{ route('backend.taxes.index_list') }}",
        type: "GET",
        data: { vendor_id: vendor_id },
        success: function(data) {
            $('#tax_id').empty(); 
            if (data.length === 0) {
                $('#tax_id').append('<option value="" disabled>{{ __("messages.no_result_found") }}</option>');
            } else {
                $('#tax_id').append('<option value="" disabled>{{ __("messages.select_tax") }}</option>');

                $.each(data, function(key, value) {
                    // Check if the value type is percentage and append '%' accordingly
                    let percentageDisplay = value.type === 'Percentage' ? value.value + '%' : currencyFormat(value.value);
                    $('#tax_id').append('<option value="' + value.id + '">' + value.title + ' (' + percentageDisplay + ')</option>');
                });
            }
            $('#tax_id').trigger('change'); 
        },
        error: function() {
            $('#tax_id').empty().append('<option value="" disabled>{{ __("messages.no_result_found") }}</option>').trigger('change');
        }

        
    });
}

    

        loadTax();
         // Handle lab loading based on vendor selection
        $('#vendor_id').on('change', function() {
            let vendorId = $(this).val();
        
           if (vendorId) {
                // Load vendor-specific labs
                loadTax(vendorId);
            } else {
                // Load all labs when no vendor is selected
                loadTax();
            } 
           
        });


        // Add this to your existing $(document).ready function

    // ...existing code...
    let hasValidationErrors = false;
    // Phone number validation on blur
    // $('#phone_number').on('blur', function() {
    //     const phone = $(this).val();
    //     if (phone) {
    //         $.ajax({
    //             url: "{{ route('backend.labs.check_unique') }}",
    //             type: "POST",
    //             data: {
    //                 _token: "{{ csrf_token() }}",
    //                 field: 'phone_number',
    //                 value: phone,
    //                 id: $('input[name="id"]').val()
    //             },
    //             success: function(response) {
    //                 if (!response.unique) {
    //                     showFieldError('phone_number', 'This phone number is already registered with another lab');
    //                     hasValidationErrors = true;
    //                 } else {
    //                     removeFieldError('phone_number');
    //                     hasValidationErrors = false;
    //                 }
    //             }
    //         });
    //     }
    // });

    // Email validation on blur
    $('#email').on('blur', function() {
        const email = $(this).val();
        if (!email) {
        showFieldError('email', '{{ __("messages.validation_required", ["field" => __("messages.email")]) }}');
        hasValidationErrors = true;
        return;
    }
    if (!validateEmail(this)) {
        hasValidationErrors = true;
        return;
    }

        // if (email) {
        //     $.ajax({
        //         url: "{{ route('backend.labs.check_unique') }}",
        //         type: "POST",
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //             field: 'email',
        //             value: email,
        //             id: $('input[name="id"]').val()
        //         },
        //         success: function(response) {
        //             if (!response.unique) {
        //                 showFieldError('email', '{{ __("messages.email_already_exists") }}');
        //                 hasValidationErrors = true;
        //             } else {
        //                 removeFieldError('email');
        //                 hasValidationErrors = false;
        //             }
        //         }
        //     });
        // }
    });

    // Helper functions to show/remove error messages
    function showFieldError(fieldId, message) {
        removeFieldError(fieldId);
        const errorDiv = `<div class="invalid-feedback field-error-${fieldId}" id="${fieldId}-error">${message}</div>`;
        $(`#${fieldId}`).addClass('is-invalid').after(errorDiv);
    }

    function removeFieldError(fieldId) {
        const field = $(`#${fieldId}`);
        field.removeClass('is-invalid');
        field.siblings('.text-danger').remove();
    
        $(`.field-error-${fieldId}`).remove();
    }
    function validateEmail(emailInput) {
    // If emailInput is a jQuery object, get the DOM element
    const input = emailInput instanceof jQuery ? emailInput[0] : emailInput;
    const invalidFeedback = $(input).siblings('.invalid-feedback');
    
    // Clear previous validation states
    $(input).removeClass('is-invalid is-valid');
    invalidFeedback.text('').hide();
    
    const email = input.value;
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Required validation
    if (!email) {
        $(input).addClass('is-invalid');
        invalidFeedback.text('{{ __("messages.email_required") }}').show();
        return false;
    }

    // Format validation
    if (!emailRegex.test(email)) {
        $(input).addClass('is-invalid');
        invalidFeedback.text('{{ __("messages.please_enter_a_valid_email_format") }}').show();
        return false;
    }

    // If all validations pass
    $(input).addClass('is-valid');
    return true;
}

       
    });
</script>
@endpush
