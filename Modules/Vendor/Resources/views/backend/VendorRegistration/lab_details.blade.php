<div class="card">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data" class='requires-validation vendor-form' id="step-form" novalidate>
            @csrf
            <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $vendor_id ?? session('vendor_id') ?? '' }}">
            <input type="hidden" name="status" value="1">
            <!-- Basic Information -->
            <h4 class="mb-3">{{ __('messages.basic_information') }}</h4>
            <div class="card">
                <div class="card-body bg-body">
                    <div class="row gy-4">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.lab_logo') }}</label>
                                <div class="btn-file-upload">
                                    <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                        <img id="imagePreview" src="{{ asset('images/default-logo.png') }}" 
                                            alt="Lab Logo" class="img-thumbnail avatar-150 object-cover">
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center text-center gap-3">
                                        <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                                            {{ __('messages.upload_image') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" id="removeButton" style="display: none;">
                                            {{ __('messages.remove_image') }}
                                        </button>
                                    </div>
                                    <input type="file" name="logo" id="logoInput" class="form-control d-none" accept="image/png,image/jpeg">
                                    @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>    
                        </div>
                        <div class="col-lg-8">
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ __('messages.lbl_lab_name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('messages.enter_lab_name') }}"
                                        value="{{ old('name') }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback ">{{ __('messages.lab_name_required') }}</div>
                                </div>
            
                                <div class="col-md-6">
                                    <label for="lab_code" class="form-label">{{ __('messages.lab_code') }}</label>
                                    <input type="text" class="form-control" name="lab_code" id="lab_code" 
                                        value="{{ old('lab_code') }}" placeholder="{{ __('messages.enter_lab_code') }}">
                                    @error('lab_code') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.lab_code_required') }}</div>
                                </div>
            
                                <div class="col-md-12">
                                    <label for="description" class="form-label">{{ __('messages.lbl_description') }}</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" placeholder="{{ __('messages.enter_description') }}">{{ old('description') }}</textarea>
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.description_required') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Vendor & Tax Information -->
            <h4 class="mb-3">{{ __('messages.tax_information') }}</h4>
            <div class="card">
                <div class="card-body bg-body">
                    <div class="row gy-3">
                        
                        @php
                            $isVendor =true;
                        @endphp
        
                        <div class="col-lg-6">
                            <label for="tax_id" class="form-label">{{ __('messages.tax') }}<span class="text-danger">*</span></label>
                            <select name="tax_id[]" class="form-select select2" id="tax_id" multiple required data-placeholder="{{ __('messages.select_tax') }}">  
                            @foreach($taxes as $tax)
                                    @php
                                        $symbol = $tax->type == 'Percentage' ? '%' : getCurrencySymbol();
                                    @endphp
                                    <option value="{{ $tax->id }}" {{ in_array($tax->id, old('tax_id', [])) ? 'selected' : '' }}>
                                        {{ $tax->title }} ({{ $tax->value }}{{ $symbol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tax_id') <span class="text-danger">{{ $message }}</span> @enderror
                            @error('tax_id.*') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.tax_required') }}</div>
                        </div>
        
                        <div class="col-lg-6">
                            <label for="tax_identification_number" class="form-label">{{ __('messages.tax_identification_number') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tax_identification_number" id="tax_identification_number" 
                                value="{{ old('tax_identification_number') }}" placeholder="{{ __('messages.enter_tax_identification_number') }}" required>
                            @error('tax_identification_number') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.tax_identification_number_required') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <h4 class="mb-3">{{ __('messages.contact_information') }}</h4>
            <div class="card">
                <div class="card-body bg-body">
                    <div class="row gy-3">
                        
                        <div class="col-md-4">
                            <label for="phone_number" class="form-label">{{ __('messages.lbl_phone_number') }}<span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone_number" id="phone_number" 
                                value="{{ old('phone_number') }}" placeholder="{{ __('messages.enter_phone_number') }}" required>
                            @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.phone_number_required') }}</div>
                        </div>
        
                        <div class="col-md-4">
                            <label for="email" class="form-label">{{ __('messages.lbl_email') }}<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" 
                                value="{{ old('email') }}" placeholder="{{ __('messages.enter_email') }}" required>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.email_required') }}</div>
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
            <h4 class="mb-3">{{ __('messages.address_information') }}</h4>
            <div class="card">
                <div class="card-body bg-body">
                    <div class="row gy-3">
                        
                        <div class="col-lg-6">
                            <label for="address_line_1" class="form-label">{{ __('messages.address_line_1') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="{{ __('messages.enter_address_line_1') }}" name="address_line_1" id="address_line_1" 
                                value="{{ old('address_line_1') }}" required>
                            @error('address_line_1') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.address_line_1_required') }}</div>
                        </div>
        
                        <div class="col-lg-6">
                            <label for="address_line_2" class="form-label">{{ __('messages.address_line_2') }}</label>
                            <input type="text" class="form-control" placeholder="{{ __('messages.enter_address_line_2') }}" name="address_line_2" id="address_line_2" 
                                value="{{ old('address_line_2') }}">
                            @error('address_line_2') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.address_line_2_required') }}</div>
                        </div>

        
                        <div class="col-lg-3 col-md-6 ">
                            <label for="country" class="form-label">{{ __('messages.country') }}<span class="text-danger">*</span></label>
                            <select name="country_id" class="form-select select2" id="country" required>
                                <option value="" disabled selected>{{ __('messages.select_country') }}</option>
                                @foreach($countries as $id => $name)
                                    <option value="{{ $id }}" {{ old('country_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.country_required') }}</div>
                        </div>
        
                        <div class="col-lg-3 col-md-6">
                            <label for="state" class="form-label">{{ __('messages.state') }}<span class="text-danger">*</span></label>
                            <select name="state_id" class="form-select select2" id="state" required>
                                <option value="" disabled selected>{{ __('messages.select_state') }}</option>
                            </select>
                            @error('state_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.state_required') }}</div>
                        </div>
        
                        <div class="col-lg-3 col-md-6">
                            <label for="city" class="form-label">City<span class="text-danger">*</span></label>
                            <select name="city_id" class="form-select select2" id="city" required>
                                <option value="" disabled selected>{{ __('messages.city') }}</option>
                            </select>
                            @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.city_required') }}</div>
                        </div>
        
                        <div class="col-lg-3 col-md-6">
                            <label for="postal_code" class="form-label">{{ __('messages.postal_code') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="postal_code" id="postal_code" 
                                value="{{ old('postal_code') }}" placeholder="{{ __('messages.enter_postal_code') }}" required>
                            @error('postal_code') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.postal_code_required') }}</div>
                        </div>
        
                        
        
                        <!-- Hidden Geolocation Fields -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>
                </div>
            </div>


            <!-- License Information -->
            <h4 class="mb-3">{{ __('messages.license_information') }}</h4>
            <div class="card">
                <div class="card-body bg-body">
                    <div class="row gy-3">
                        
                        <div class="col-lg-4">
                            <label for="license_number" class="form-label">{{ __('messages.license_number') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="license_number" id="license_number" 
                                value="{{ old('license_number') }}" placeholder="{{ __('messages.enter_license_number') }}" required>
                            @error('license_number') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.license_number_required') }}</div>
                        </div>
        
                        <div class="col-lg-4">
                            <label for="license_document" class="form-label">{{ __('messages.license_document') }}<span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="license_document" id="license_document" 
                                accept="application/pdf,image/jpeg" required>
                            @error('license_document') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.license_document_required') }}</div>
                        </div>
        
                        <div class="col-lg-4">
                            <label for="license_expiry_date" class="form-label">{{ __('messages.license_expiry_date') }}<span class="text-danger">*</span></label>
                            <input type="date" class="form-control datetimepicker" name="license_expiry_date" id="license_expiry_date" 
                                value="{{ old('license_expiry_date') }}" placeholder="{{ __('messages.enter_license_expiry_date') }}" required>
                            @error('license_expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.license_expiry_date_required') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accreditation Information -->
            <h4 class="mb-3">{{ __('messages.accreditation_information') }}</h4>
            <div class="card">
                <div class="card-body  bg-body">
                    <div class="row gy-3">
                        
                        <div class="col-lg-4">
                            <label for="accreditation_type" class="form-label">{{ __('messages.accreditation_type') }}</label>
                            <select name="accreditation_type" id="accreditation_type" class="form-select select2">
                                <option value="" disabled selected>{{ __('messages.select_accreditation_type') }}</option>
                                <option value="NABL" {{ old('accreditation_type') == 'NABL' ? 'selected' : '' }}>NABL</option>
                                <option value="ISO" {{ old('accreditation_type') == 'ISO' ? 'selected' : '' }}>ISO</option>
                            </select>
                            @error('accreditation_type') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.accreditation_type_required') }}</div>
                        </div>
        
                        <div class="col-lg-4">
                            <label for="accreditation_certificate" class="form-label">{{ __('messages.accreditation_certificate') }}</label>
                            <input type="file" class="form-control" name="accreditation_certificate" id="accreditation_certificate" 
                                accept="application/pdf,image/jpeg">
                            @error('accreditation_certificate') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.accreditation_certificate_required') }}</div>
                        </div>
        
                        <div class="col-lg-4">
                            <label for="accreditation_expiry_date" class="form-label">{{ __('messages.accreditation_expiry_date') }}</label>
                            <input type="date" class="form-control datetimepicker" name="accreditation_expiry_date" id="accreditation_expiry_date" 
                                value="{{ old('accreditation_expiry_date') }}" placeholder="{{ __('messages.enter_accreditation_expiry_date') }}">
                            @error('accreditation_expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.accreditation_expiry_date_required') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <h4 class="mb-3">{{ __('messages.payment_information') }}</h4>
            <div class="card">
                <div class="card-body bg-body">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <label class="form-label d-block">{{ __('messages.payment_modes_accepted') }} <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="payment_modes[]" 
                                    id="payment_manual" value="manual" {{ in_array('manual', old('payment_modes', ['manual'])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_manual">{{ __('messages.manual') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="payment_modes[]" 
                                    id="payment_online" value="online" {{ in_array('online', old('payment_modes', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_online">{{ __('messages.online') }}</label>
                            </div>
                            @error('payment_modes') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.payment_modes_accepted') }}</div>
                        </div>
        
                        <div class="col-md-12" id="online_payment_gateways" style="{{ in_array('online', old('payment_modes', [])) ? '' : 'display: none;' }}">
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
                                            {{ in_array($gateway->id, old('payment_gateways', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gateway_{{ $gateway->id }}">
                                        @php
                                            $displayName = match($gateway->type) {
                                                'razorpayPayment' => 'Razorpay',
                                                'stripePayment' => 'Stripe',
                                                'paystackPayment' => 'Paystack',
                                                'paypalPayment' => 'PayPal',
                                                'flutterwavePayment' => 'Flutterwave',
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

          

            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-primary next-btn float-end">
                    {{__('messages.next_step')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@include('layouts.script')
<script>


        document.getElementById('phone_number').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        // Logo Image Handling
    $(document).ready(function() {
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
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                });
            }

            // Country-State-City Dependent Dropdowns
            $('#country').on('change', function() {
                var countryId = this.value;
                $("#state").html('');
                $.ajax({
                    url: "{{route('backend.state.index_list', ['countryId' => '__countryId__'])}}".replace('__countryId__', countryId)  ,
                    type: "GET",
                    data: {
                        country_id: countryId
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#state').html('<option value="">Select State</option>');
                        $.each(result, function(key, value) {
                            $("#state").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#city').html('<option value="">Select City</option>');
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
                        $('#city').html('<option value="">Select City</option>');
                        $.each(result, function(key, value) {
                            $("#city").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
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



       
    });
</script>