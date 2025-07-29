@extends('backend.layouts.app')
@section('title'){{__($module_title)}}@endsection


@section('content')
<div class="form-content">
    <form action="{{ route('backend.labs.update', $data->id) }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
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
                    @method('PUT')
                    {{ html()->hidden('id',$data->id ?? null) }}
                    <!-- Basic Information -->
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.lab_logo') }}</label>
                                <div class="btn-file-upload">
                                    <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                        <img id="imagePreview" 
                                            src="{{ $data->getLogoUrlAttribute() ?? asset('images/default-avatar.png') }}" 
                                            alt="logo Image" class="img-thumbnail avatar-150 object-cover">
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
                                <input type="file" name="logo" id="logoImageInput" class="form-control d-none" accept="image/*">
                                @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mt-4">
                                <label for="status" class="form-label">{{ __('messages.lbl_status') }}</label>
                                <div class="d-flex align-items-center justify-content-between form-control">
                                    <label for="status" class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" 
                                            value="1" {{ old('status', $data->status) == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>                       

                        <div class="col-md-8">
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ __('messages.lbl_lab_name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" 
                                        value="{{ old('name', $data->name) }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="lab_code" class="form-label">{{ __('messages.lab_code') }}</label>
                                    <input type="text" class="form-control" name="lab_code" id="lab_code" 
                                        value="{{ old('lab_code', $data->lab_code) }}">
                                    @error('lab_code') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="description" class="form-label">{{ __('messages.lbl_description') }}</label>
                                    <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $data->description) }}</textarea>
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                   

                    

                    

                   

                                         
            </div>
        </div>

        <!-- Vendor & Tax Information -->
        @if(multivendor() == 1)
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{ __('messages.vendor_tax_information') }}</h4>
            </div>
            @else
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{ __('messages.tax_information') }}</h4>
            </div>
            @endif

        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    @php
                        $isVendor = auth()->user()->user_type === 'vendor';
                    @endphp
                    @if(!$isVendor &&  multivendor()==1)
                    <div class="col-md-4">
                        <label for="vendor_id" class="form-label">{{ __('messages.select_vendor') }}<span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendor_id" class="form-select select2" required>
                            <!-- Default Option -->
                            <option value="" disabled {{ old('vendor_id', $data->vendor_id) == null || !$vendors->contains('id', old('vendor_id', $data->vendor_id)) ? 'selected' : '' }}>
                            {{ __('messages.select_vendor') }}
                            </option>
                    
                            <!-- Vendor Options -->
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id', $data->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->first_name }}{{ $vendor->last_name ? ' ' . $vendor->last_name : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>                                                                                               
                    @else
                        {{-- Hidden input for vendor users --}}
                        <input type="hidden" name="vendor_id" value="{{ auth()->id() }}">
                    @endif

                    <div class="col-md-4">
                        <label for="tax_id" class="form-label">{{ __('messages.lbl_tax') }}</label>
                        <select name="tax_id[]" id="tax_id" class="form-select select2" multiple>
                            @foreach($taxes as $tax)
                                <option value="{{ $tax->id }}" 
                                    {{ in_array($tax->id, old('tax_id', $data['tax'] ?? [])) ? 'selected' : '' }}>
                                    {{ $tax->title }} ({{ $tax->value }}%)
                                </option>
                            @endforeach
                        </select>
                        @if(multivendor() == 1)
                        <small class="text-muted">{{ __('messages.default_tax_applied') }}</small>
                        @endif
                        @error('tax_id') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="tax_identification_number" class="form-label">{{ __('messages.tax_identification_number') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tax_identification_number" id="tax_identification_number" 
                            value="{{ old('tax_identification_number', $data->tax_identification_number) }}" required>
                        @error('tax_identification_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.other_information') }}</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <label for="phone_number" class="form-label">{{ __('messages.lbl_phone_number') }}<span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="phone_number" id="phone_number" 
                            value="{{ old('phone_number', $data->phone_number) }}" required>
                        @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">{{ __('messages.lbl_email') }}<span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="email" 
                            value="{{ old('email', $data->email) }}" required>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="time_slot" class="form-label">{{ __('messages.time_slot') }}<span class="text-danger">*</span></label>
                        <select name="time_slot" id="time_slot" class="form-select select2" required>
                            <option value="" disabled selected>{{ __('messages.select_time_slot') }}</option>
                            @foreach(range(5, 60, 5) as $minutes)
                                <option value="{{ $minutes }}" {{ old('time_slot', $data->time_slot) == $minutes ? 'selected' : '' }}>{{ $minutes }} {{ __('messages.minutes') }}</option>
                            @endforeach
                        </select>
                        @error('time_slot') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.address_information') }}</h4>
        </div>
        <div class="card">
            <div class="card-body">                
                <div class="row gy-4">
                    <div class="col-md-6">
                        <label for="address_line_1" class="form-label">{{ __('messages.address_line_2') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="address_line_1" id="address_line_1" 
                            value="{{ old('address_line_1', $data->address_line_1) }}" required>
                        @error('address_line_1') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="address_line_2" class="form-label">{{ __('messages.address_line_2') }}</label>
                        <input type="text" class="form-control" name="address_line_2" id="address_line_2" 
                            value="{{ old('address_line_2', $data->address_line_2) }}">
                        @error('address_line_2') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                  

                    <div class="col-md-3">
                        <label for="country" class="form-label">{{ __('messages.country') }}<span class="text-danger">*</span></label>
                        <select name="country_id" class="form-select select2" id="country" required>
                            <option value="" disabled>{{ __('messages.select_country') }}</option>
                            @foreach($countries as $id => $name)
                                <option value="{{ $id }}" {{ old('country_id', $data->country_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="state" class="form-label">{{ __('messages.state') }}<span class="text-danger">*</span></label>
                        <select name="state_id" class="form-select select2" id="state" required>
                            <option value="" disabled>{{ __('messages.select_state') }}</option>
                            @foreach($states as $id => $name)
                                <option value="{{ $id }}" {{ old('state_id', $data->state_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('state_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="city" class="form-label">{{ __('messages.city') }}<span class="text-danger">*</span></label>
                        <select name="city_id" class="form-select select2" id="city" required>
                            <option value="" disabled>{{ __('messages.select_city') }}</option>
                            @foreach($cities as $id => $name)
                                <option value="{{ $id }}" {{ old('city_id', $data->city_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="postal_code" class="form-label">{{ __('messages.postal_code') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="postal_code" id="postal_code" 
                            value="{{ old('postal_code', $data->postal_code) }}" required>
                        @error('postal_code') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Hidden Geolocation Fields -->
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $data->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $data->longitude) }}">
                </div>
            </div>
        </div>

        <!-- License Information -->
        <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.license_information') }}</h4>
        </div>
        <div class="card">
            <div class="card-body">                 
                 <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="license_number" class="form-label">{{ __('messages.license_number') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="license_number" id="license_number" 
                            value="{{ old('license_number', $data->license_number) }}" required>
                        @error('license_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="license_document" class="form-label">{{ __('messages.license_document') }}<span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="license_document" id="license_document" 
                            accept="application/pdf,image/jpeg,image/jpg,image/png">
                        @if($data->getFirstMedia('license_document'))
                            <div class="mt-2">
                                <a href="{{ $data->getFirstMediaUrl('license_document') }}" target="_blank" class="text-primary">
                                    <i class="fas fa-file-alt"></i> {{ __('messages.view_current_document') }}
                                </a>
                            </div>
                        @endif
                        @error('license_document') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="license_expiry_date" class="form-label">{{ __('messages.license_expiry_date') }}<span class="text-danger">*</span></label>
                        <input type="date" class="form-control datetimepicker" name="license_expiry_date" id="license_expiry_date" 
                            value="{{ old('license_expiry_date', $data->license_expiry_date ? date('Y-m-d', strtotime($data->license_expiry_date)) : '') }}" required>
                        @error('license_expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Accreditation Information -->
        <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.accreditation_information') }}</h4>
        </div>
        <div class="card">
            <div class="card-body">                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="accreditation_type" class="form-label">{{ __('messages.accreditation_type') }}</label>
                        <select name="accreditation_type" id="accreditation_type" class="form-select select2">
                            <option value="">{{ __('messages.accreditation_type') }}</option>
                            <option value="NABL" {{ old('accreditation_type', $data->accreditation_type) == 'NABL' ? 'selected' : '' }}>{{ __('messages.nabl') }}</option>
                            <option value="ISO" {{ old('accreditation_type', $data->accreditation_type) == 'ISO' ? 'selected' : '' }}>{{ __('messages.iso') }}</option>
                        </select>
                        @error('accreditation_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="accreditation_certificate" class="form-label">{{ __('messages.accreditation_certificate') }}</label>
                        <input type="file" class="form-control" name="accreditation_certificate" id="accreditation_certificate" 
                            accept="application/pdf,image/jpeg,image/jpg,image/png">
                        @if($data->getFirstMedia('accreditation_certificate'))
                            <div class="mt-2">
                                <a href="{{ $data->getFirstMediaUrl('accreditation_certificate') }}" target="_blank" class="text-primary">
                                    <i class="fas fa-file-alt"></i> {{__('messages.view_current_certificate')}}
                                </a>
                            </div>
                        @endif
                        @error('accreditation_certificate') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="accreditation_expiry_date" class="form-label">{{ __('messages.accreditation_expiry_date') }}</label>
                        <input type="date" class="form-control datetimepicker" name="accreditation_expiry_date" id="accreditation_expiry_date" 
                            value="{{ old('accreditation_expiry_date', $data->accreditation_expiry_date ? date('Y-m-d', strtotime($data->accreditation_expiry_date)) : '') }}">
                        @error('accreditation_expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div> 
            </div>
        </div>

        <!-- Payment Information -->
        <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.payment_information') }}</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label class="form-label d-block">{{ __('messages.payment_modes_accepted') }}<span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="payment_modes[]" 
                                id="payment_manual" value="manual" 
                                {{ in_array('manual', old('payment_modes', $data->payment_modes ?? [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_manual">{{ __('messages.manual') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="payment_modes[]" 
                                id="payment_online" value="online"
                                {{ in_array('online', old('payment_modes', $data->payment_modes ?? [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_online">{{ __('messages.online') }}</label>
                        </div>
                        @error('payment_modes') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-12 mb-3" id="online_payment_gateways" style="{{ in_array('online', old('payment_modes', $data->payment_modes ?? [])) ? '' : 'display: none;' }}">
                        <label class="form-label d-block">{{ __('messages.online_payment_gateways') }}</label>
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
                                    {{ in_array($gateway->type, old('payment_gateways', $data->payment_gateways ?? [])) ? 'checked' : '' }}>
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
                        @error('payment_gateways') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                </div>
            </div>
        </div>


        <div class="d-flex align-items-center justify-content-end">
            <button type="submit" class="btn btn-primary">
                {{__('messages.update')}}
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


        const $uploadButton = $('#uploadButton');
const $removeButton = $('#removeButton');
const $logoImageInput = $('#logoImageInput');
const $imagePreview = $('#imagePreview');

$uploadButton.on('click', function() {
    $logoImageInput.trigger('click');
});

$logoImageInput.on('change', function() {
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
    $logoImageInput.val('');
    $imagePreview.attr('src', '{{ asset("images/default-avatar.png") }}');
    $(this).hide();
});

    // Add inside your $(document).ready(function() {...})

    // Initialize select all state based on existing selections
    function updateSelectAllState() {
        const allChecked = $('.gateway-checkbox:checked').length === $('.gateway-checkbox').length;
        $('#select_all_gateways').prop('checked', allChecked);
    }

    // Select All functionality for payment gateways
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
        updateSelectAllState();
        
        // Remove error message if any gateway is selected
        if ($('.gateway-checkbox:checked').length > 0) {
            const errorMessage = document.querySelector('#gateway-error');
            if (errorMessage) {
                errorMessage.remove();
            }
        }
    });

    // Initialize select all state on page load
    updateSelectAllState();

        // Payment Mode Handling
        $('#payment_online').change(function() {
            $('#online_payment_gateways').toggle(this.checked);
        });

        // Country-State-City Dependent Dropdowns
        $('#country').on('change', function() {
            var countryId = this.value;
            $("#state").html('');
            $.ajax({
                url: "{{route('backend.state.index_list', ['countryId' => '__countryId__'])}}".replace('__countryId__', countryId),
                type: "GET",
                data: {
                    country_id: countryId
                },
                dataType: 'json',
                success: function(result) {
                    $('#state').html("<option value="">{{ __('messages.select_state') }}</option>");
                    $.each(result, function(key, value) {
                        $("#state").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    $('#city').html("<option value="">{{ __('messages.select_city') }}</option>");
                }
            });
        });

        $('#state').on('change', function() {
            var stateId = this.value;
            $("#city").html('');
            $.ajax({
                url: "{{route('backend.city.index_list',['stateId' => '__stateId__'])}}".replace('__stateId__', stateId),
                type: "GET",
                data: {
                    state_id: stateId
                },
                dataType: 'json',
                success: function(result) {
                    $('#city').html("<option value="">{{ __('messages.select_city') }}</option>");
                    $.each(result, function(key, value) {
                        $("#city").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        });

        // Form Validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.requires-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Get the form element
        const form = document.getElementById('form-submit');
        
        form.addEventListener('submit', function(event) {
            // Check if online payment is selected
            const onlinePaymentChecked = document.querySelector('input[name="payment_modes[]"][value="online"]').checked;
            
            if (onlinePaymentChecked) {
                // Check if any payment gateway is selected
                const selectedGateways = document.querySelectorAll('input[name="payment_gateways[]"]:checked');
                
                if (selectedGateways.length === 0) {
                    event.preventDefault();
                    // Add error message to the payment gateways section
                    const errorDiv = document.createElement('span');
                    errorDiv.className = 'text-danger d-block mt-2';
                    errorDiv.textContent = "{{ __('messages.pls_select_payment_gateway') }}";
                    
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
            }
            if (hasValidationErrors) {
                e.preventDefault();
                alert("{{ __('messages.pls_select_payment_gateway') }}");
                return false;
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


        // Function to load taxes dynamically based on vendor_id
           // Assume this is populated from the backend or initialized in your Blade template
        const preSelectedTaxes = {!! json_encode($data->tax ?? []) !!};

        function loadTax(vendorId = null) {
            $.ajax({
                url: "{{ route('backend.taxes.index_list') }}",
                type: "GET",
                data: { vendor_id: vendorId },
                success: function(data) {
                    const taxDropdown = $('#tax_id');
                    taxDropdown.empty(); // Clear existing options
                    taxDropdown.append("<option value="" disabled>{{ __('messages.select_tax') }}</option>"); // Default placeholder

                    $.each(data, function(key, value) {
                        // Determine the display for percentage-based taxes
                        const percentageDisplay = value.type === 'Percentage' ? `${value.value}%` : currencyFormat(value.value);

                        // Check if this tax ID should be pre-selected
                        const isSelected = preSelectedTaxes.includes(value.id) ? 'selected' : '';

                        taxDropdown.append(`<option value="${value.id}" ${isSelected}>${value.title} (${percentageDisplay})</option>`);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('{{ __("messages.error_loading_taxes") }}', error);
                }
            });
        }

        // Initial load of taxes
        loadTax();

        // Handle dynamic tax loading on vendor selection
        $('#vendor_id').on('change', function() {
            const vendorId = $(this).val();
            

            if (vendorId) {
                loadTax(vendorId); // Load vendor-specific taxes
            } else {
                loadTax(); // Load general taxes
            }
        });



                // Add this inside your $(document).ready(function() {...})
        let hasValidationErrors = false;

        // Phone number validation on blur
        // $('#phone_number').on('blur', function() {
        //     const phone = $(this).val();
        //     const currentId = $('input[name="id"]').val();
            
        //     if (phone) {
        //         $.ajax({
        //             url: "{{ route('backend.labs.check_unique') }}",
        //             type: "POST",
        //             data: {
        //                 _token: "{{ csrf_token() }}",
        //                 field: 'phone_number',
        //                 value: phone,
        //                 id: currentId // Pass current record ID to ignore it in uniqueness check
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
        // $('#email').on('blur', function() {
        //     const email = $(this).val();
        //     const currentId = $('input[name="id"]').val();
        //     if (!validateEmail(this)) {
        //         hasValidationErrors = true;
        //         return;
        //     }
        //     if (email) {
        //         $.ajax({
        //             url: "{{ route('backend.labs.check_unique') }}",
        //             type: "POST",
        //             data: {
        //                 _token: "{{ csrf_token() }}",
        //                 field: 'email',
        //                 value: email,
        //                 id: currentId // Pass current record ID to ignore it in uniqueness check
        //             },
        //             success: function(response) {
        //                 if (!response.unique) {
        //                     showFieldError('email', 'This email is already registered with another lab');
        //                     hasValidationErrors = true;
        //                 } else {
        //                     removeFieldError('email');
        //                     hasValidationErrors = false;
        //                 }
        //             }
        //         });
        //     }
        // });

    // Helper functions
    function showFieldError(fieldId, message) {
        removeFieldError(fieldId);
        const errorDiv = `<div class="text-danger field-error-${fieldId}">${message}</div>`;
        $(`#${fieldId}`).addClass('is-invalid').after(errorDiv);
    }

    function removeFieldError(fieldId) {
        $(`#${fieldId}`).removeClass('is-invalid');
        $(`.field-error-${fieldId}`).remove();
    }


    });
    document.getElementById('email').addEventListener('blur', function() {
    validateEmail(this);
});

function validateEmail(emailInput) {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    let isValid = true;

    // Remove any existing error messages
    removeFieldError('email');
    
    // Check if empty
    if (!emailInput.value) {
        showFieldError('email', '{{ __("messages.email_required") }}');
        isValid = false;
        return false;
    }
    
    // Check format
    if (!emailRegex.test(emailInput.value)) {
        showFieldError('email', '{{ __("messages.please_enter_a_valid_email_format") }}');
        isValid = false;
        return false;
    }

    // Check uniqueness
    // const currentId = $('input[name="id"]').val();
    // $.ajax({
    //     url: "{{ route('backend.labs.check_unique') }}",
    //     type: "POST",
    //     async: false,
    //     data: {
    //         _token: "{{ csrf_token() }}",
    //         field: 'email',
    //         value: emailInput.value,
    //         id: currentId
    //     },
    //     success: function(response) {
    //         if (!response.unique) {
    //             showFieldError('email', 'This email is already registered with another lab');
    //             isValid = false;
    //         } else {
    //             removeFieldError('email');
    //             $(emailInput).addClass('is-valid');
    //         }
    //     },
    //     error: function() {
    //         showFieldError('email', 'Error checking email availability');
    //         isValid = false;
    //     }
    // });
    
    return isValid;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Email validation on blur
    document.getElementById('email').addEventListener('blur', function() {
        validateEmail(this);
    });

    // Form submission
    const form = document.getElementById('form-submit');
    form.addEventListener('submit', function(e) {
        const emailInput = document.getElementById('email');
        if (!validateEmail(emailInput)) {
            e.preventDefault();
            emailInput.focus();
        }
    });
});

// Helper functions
function showFieldError(fieldId, message) {
    removeFieldError(fieldId);
    const errorDiv = `<div class="text-danger field-error-${fieldId}">${message}</div>`;
    $(`#${fieldId}`).addClass('is-invalid').after(errorDiv);
}

function removeFieldError(fieldId) {
    $(`#${fieldId}`).removeClass('is-invalid is-valid');
    $(`.field-error-${fieldId}`).remove();
}
</script>
@endpush
