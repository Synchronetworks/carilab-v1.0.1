@extends('backend.layouts.app')
@section('title') {{ __($module_title) }}@endsection
@section('content')
<div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
        <a href="{{ route('backend.coupons.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div>
    <div class="form-content">
        {{ html()->form('POST', route('backend.coupons.update', $coupon->id))
        ->attribute('enctype', 'multipart/form-data')
        ->attribute('data-toggle', 'validator')
        ->id('form-submit')
        ->open() }}
        @csrf
        <div class="card">
            <div class="card-body">
                @method('PUT')
                <div class="row gy-4">
                    <!-- Vendor Selection -->
                    @if(multivendor() == 1 && auth()->user()->user_type != 'vendor')
                        <div class="col-sm-6 col-md-4">
                            <label for="vendor_id" class="form-label">{{__('messages.select_vendor') }}</label>
                            <select name="vendor_id" id="vendor_id" class="form-control">
                                <option value="">{{ __('messages.select_vendor_optional'), }}</option>
                                @foreach ($vendors ?? [] as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $coupon->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->full_name ?? $vendor->first_name . ' ' . $vendor->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <input type="hidden" name="vendor_id" value="{{ auth()->id() }}">
                    @endif
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label">{{__('messages.lab')}}<span class="text-danger">*</span></label>
                        <select class=" form-control  select2" name="lab_id" id="lab_id" required>
                            <option value="" disabled selected>{{ __('messages.select_lab') }}</option>
                        </select>
                        @error('lab_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.lab_required') }}</div>
                    </div>
                    <!-- Coupon Code -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_coupon_code') . ' <span class="text-danger">*</span>', 'coupon_code')->class('form-label') }}
                        {{ html()->text('coupon_code', old('coupon_code', $coupon->coupon_code))
                        ->placeholder(__('Enter coupon code'))
                        ->class('form-control') }}
                        @error('coupon_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Discount Type -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_discount_type') . ' <span class="text-danger">*</span>', 'discount_type')->class('form-label') }}
                        {{ html()->select('discount_type', ['percentage' => __('messages.percentage'), 'fixed' => __('messages.fixed')], old('discount_type', $coupon->discount_type))
                        ->placeholder(__('messages.enter_discount_value'))
                        ->class('form-control') }}
                        @error('discount_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                     <!-- Discount Value -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_discount_value') .'<span class=" type-symbol"> (%) </span>'. ' <span class="text-danger">*</span>', 'discount_value')->class('form-label') }}
                        {{ html()->number('discount_value',old('discount_value', $coupon->discount_value))
                            ->placeholder(__('messages.enter_discount_value'))
                            ->class('form-control')
                            ->attribute('min', '0')
                            ->attribute('step', 'any') }}
                        @error('discount_value')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    

                    <!-- Applicability -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.applicability') . ' <span class="text-danger">*</span>', 'applicability')->class('form-label') }}
                        {{ html()->select('applicability[]', [
                            'specific_tests' => __('messages.specific_tests'),
                            'specific_packages' => __('messages.specific_packages'),
                            'all' => __('messages.all_tests_packages')
    ], old('applicability', $coupon->applicability))
        ->multiple()
        ->id('applicability')
        ->class('form-select select2') }}
                        @error('applicability')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                 

                    @php
                    $selectedTests = old('test_id', $coupon->tests ? $coupon->tests->pluck('test_id')->map(fn($id) => (string) $id)->toArray() : []);

                    // Get the selected package IDs from the coupon
                    $selectedPackages = old('package_id', $coupon->packages ? $coupon->packages->pluck('package_id')->map(fn($id) => (string) $id)->toArray() : []);
                    @endphp
                    
                    <div class="col-sm-6 col-md-4" id="test_select" style="display: none;">
                        <label class="form-label">{{ __('messages.lbl_test') }}<span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input type="checkbox" id="select_all_tests" class="form-check-input">
                            <label for="select_all_tests" class="form-check-label">{{__('messages.select_all_tests')}}</label>
                        </div>
                        <select class=" form-control  select2" name="test_id[]" id="test_id" multiple>
                            @foreach($tests as $test)
                            <option value="{{ $test->id }}" {{ in_array($test->id, $selectedTests) ? 'selected' : '' }}>
                                {{ $test->name }} - {{ \Currency::format($test->price) }}
                            </option>
                        @endforeach
                        </select>
                        @error('test_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.test_required') }}</div>
                    </div>

                   
                     <!-- Select Package (conditionally shown) -->
                     <div class="col-sm-6 col-md-4 mb-3" id="package_select"
                     style="{{ in_array('specific_packages', old('package_id', $coupon->package_id ?? [])) ? 'display:block;' : 'display:none;' }}">
                     {{ html()->label(__('messages.select_package') . ' <span class="text-danger">*</span>', 'package_id')->class('form-label') }}
                     <div class="form-check">
                        <input type="checkbox" id="select_all_packages" class="form-check-input">
                        <label for="select_all_packages" class="form-check-label">{{__('messages.select_all_packages')}}</label>
                    </div>
                     <select name="package_id[]" id="package_id" class="form-select select2" multiple>
                         @foreach ($packages as $package)
                             <option value="{{ $package->id }}" {{ in_array((string) $package->id, $selectedPackages) ? 'selected' : '' }}>
                                 {{ $package->name }} - {{ \Currency::format($package->price) }}
                             </option>
                         @endforeach
                     </select>

                     @error('package_id')
                         <span class="text-danger">{{ $message }}</span>
                     @enderror
                 </div>

                    <!-- Start At -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_start_at') . ' <span class="text-danger">*</span>', 'start_at')->class('form-label') }}
                        {{ html()->date('start_at', old('start_at', $coupon->start_at))->class('form-control datetimepicker')->attribute('data-min-date', 'today') ->placeholder(__('messages.placeholde_select_start_date'))  }}
                        @error('start_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                   

                    <!-- End At -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_end_at') . ' <span class="text-danger">*</span>', 'end_at')->class('form-label') }}
                        {{ html()->date('end_at', old('end_at', $coupon->end_at))->class('form-control datetimepicker')->attribute('data-min-date', 'today') ->placeholder(__('messages.placeholde_select_end_date')) }}
                        @error('end_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Usage Limits -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_total_usage_limit') . ' <span class="text-danger">*</span>', 'total_usage')->class('form-label') }}
                        {{ html()->number('total_usage_limit', old('total_usage_limit', $coupon->total_usage_limit))
        ->placeholder(__('messages.enter_total_usage'))
        ->class('form-control')
        ->attribute('min', '1') }}
                        @error('total_usage_limit')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_per_customer_usage_limit') . ' <span class="text-danger">*</span>', 'per_customer_usage')->class('form-label') }}
                        {{ html()->number('per_customer_usage_limit', old('per_customer_usage_limit', $coupon->per_customer_usage_limit))
        ->placeholder(__('messages.enter_per_customer_usage'))
        ->class('form-control')
        ->attribute('min', '1') }}
                        @error('per_customer_usage_limit')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                        <div class="d-flex align-items-center justify-content-between form-control">
                            {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                            <div class="form-check form-switch">
                                {{ html()->hidden('status', 1) }}
                                {{
        html()->checkbox('status', old('status', $coupon->status), $coupon->status == 1)
            ->class('form-check-input')
            ->id('status')
                                    }}
                            </div>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary')}}
        </div>
        {{ html()->form()->close() }}
    </div>
@endsection

@push('after-scripts')
    <script>

document.getElementById('total_usage_limit').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    document.getElementById('per_customer_usage_limit').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
        document.addEventListener("DOMContentLoaded", function () {
        let startPicker = flatpickr("#start_at", {
            dateFormat: "Y-m-d",
            minDate: "today", // Disable past dates, allow today and future dates
            onChange: function (selectedDates) {
                let minEndDate = new Date(selectedDates[0]);
                minEndDate.setDate(minEndDate.getDate() + 1); // Set end date at least one day after start date
                
                endPicker.set("minDate", minEndDate); // Update end date min selection
                endPicker.setDate(minEndDate); // Auto set end date to next day
            }
        });

        let endPicker = flatpickr("#end_at", {
            dateFormat: "Y-m-d",
            minDate: new Date().fp_incr(1), // Default min end date as tomorrow
        });
    });
        document.addEventListener('DOMContentLoaded', function () {

            function updateValueSymbol() {
            const type = $('#discount_type').val();
            
            const symbol = type === 'percentage' ? ' (%) ' :  '({{ getCurrencySymbol() }})';
            $('.type-symbol').text(symbol);
        }

        $('#per_customer_usage_limit').on('blur', function() {
        validateUsageLimits();
    });

    function validateUsageLimits() {
        const totalUsage = parseInt($('#total_usage_limit').val()) || 0;
        const perCustomerUsage = parseInt($('#per_customer_usage_limit').val()) || 0;
        const perCustomerField = $('#per_customer_usage_limit');
        
        // Remove existing error message if any
        $('.usage-limit-error').remove();
        perCustomerField.removeClass('is-invalid');
        
        if (perCustomerUsage > totalUsage) {
            const errorMessage = '{{ __("messages.per_customer_usage_limit_error") }}';
            perCustomerField.addClass('is-invalid');
            perCustomerField.after(`<div class="text-danger usage-limit-error">${errorMessage}</div>`);
            return false;
        }
        return true;
    }

    // Add form submit validation
    $('#form-submit').on('submit', function(e) {
        if (!validateUsageLimits()) {
            e.preventDefault();
            return false;
        }
    });
        // Initial setup
        updateValueSymbol();

        // Update on type change
        $('#discount_type').on('change', function() {
            
            updateValueSymbol();
        });
            // Initialize Select2 for the applicability, test, and package fields
            $('#applicability, #test_id select, #package_id select').select2({
                placeholder: "Select options",
                allowClear: true
            });

            let isChangeTriggered = false; // Flag to prevent recursive change events

            // Function to handle the display of Test/Package selection based on Applicability
            $('#applicability').on('change', function () {
                // Prevent recursion by checking the flag
                if (isChangeTriggered) {
                    return;
                }

                const selectedValues = $(this).val();

                // Hide both test and package selects by default
                $('#test_select').hide();
                $('#package_select').hide();

                // Clear previous selections in test and package dropdowns
                $('#test_id select').val(null).trigger('change');
                $('#package_id select').val(null).trigger('change');

                // If "All Tests/Packages" is selected, hide both fields
                if (selectedValues.includes('all')) {
                    $('#test_select').hide();
                    $('#package_select').hide();
                    $('#applicability').find('option[value="specific_tests"], option[value="specific_packages"]').prop('selected', false);
                    $('#applicability').trigger('change.select2');
                } else {
                    // Show test select if 'specific_tests' is selected
                    if (selectedValues.includes('specific_tests')) {
                        $('#test_select').show();
                        $('#test_id select').select2(); // Reinitialize Select2 when shown
                    }

                    // Show package select if 'specific_packages' is selected
                    if (selectedValues.includes('specific_packages')) {
                        $('#package_select').show();
                        $('#package_id select').select2(); // Reinitialize Select2 when shown
                    }
                    if (selectedValues.includes('specific_tests') && selectedValues.includes('specific_packages')) {
                        $('#applicability').find('option[value="all"]').prop('selected', false);
                        $('#applicability').trigger('change.select2');
                    }
                }
            });
 // Select All for Tests
        $('#select_all_tests').on('change', function() {
            if ($(this).is(':checked')) {
                $('#test_id option').prop('selected', true);
            } else {
                $('#test_id option').prop('selected', false);
            }
            $('#test_id').trigger('change');
        });

        // Select All for Packages
        $('#select_all_packages').on('change', function() {
            if ($(this).is(':checked')) {
                $('#package_id option').prop('selected', true);
            } else {
                $('#package_id option').prop('selected', false);
            }
            $('#package_id').trigger('change');
        });
            // Handle "Select All" logic for Test dropdown
            $('#test_id select').on('change', function () {
                if (isChangeTriggered) return; // Prevent recursion

                if ($(this).val().includes('all')) {
                    isChangeTriggered = true;
                    $(this).find('option').prop('selected', true).trigger('change');
                    isChangeTriggered = false;
                }
            });

            // Handle "Select All" logic for Package dropdown
            $('#package_id select').on('change', function () {
                if (isChangeTriggered) return; // Prevent recursion

                if ($(this).val().includes('all')) {
                    isChangeTriggered = true;
                    $(this).find('option').prop('selected', true).trigger('change');
                    isChangeTriggered = false;
                }
            });


            // Trigger change on page load to ensure the correct fields are shown
            $('#applicability').trigger('change');

            // Load Labs based on Vendor selection
            let loggedInVendorId = "{{ auth()->user()->user_type == 'vendor' ? auth()->id() : null }}";
            function loadAllLabs(vendor_id = null) {
                $.ajax({
                    url: "{{ route('backend.labs.index_list') }}",
                    type: "GET",
                    data: { vendor_id: vendor_id },
                    success: function (data) {
                        updateLabSelect(data,{{ $coupon->lab_id ?? 'null' }});
                    },
                    error: function (xhr) {
                        console.error('{{ __("messages.error_loading_labs") }}', xhr);
                    }
                });
            }
            if (loggedInVendorId) {
                loadAllLabs(loggedInVendorId);
            } else {
                loadAllLabs()
            };
            $('#vendor_id').on('change', function () {
                let vendorId = $(this).val();
                const $labSelect = $('#lab_id');
                if (vendorId) {
                    // Load vendor-specific labs
                    loadAllLabs(vendorId);
                } else {
                    // Load all labs when no vendor is selected
                    loadAllLabs();
                }
            });


            function updateLabSelect(data, selectedLabId) {
                const $labSelect = $('#lab_id');
                $labSelect.empty();
                $labSelect.append('<option value="" disabled>{{ __("messages.select_lab") }}</option>');
                $.each(data, function (key, value) {
                    let isSelected = (value.id == selectedLabId) ? 'selected' : '';
                    $labSelect.append('<option value="' + value.id + '" ' + isSelected + '>' + value.name + '</option>');
                });
            }
        });

        $('#lab_id').on('change', function() {
            loadTests();
            loadPackages();
        });

        function loadTests() {
            let labId = $('#lab_id').val();
            let selectedTestId = "{{ $coupon->test_id ?? 'null' }}";
            if (labId) {
                $.ajax({
                    url: "{{ route('backend.catlogmanagements.test_list') }}",
                    type: "GET",
                    data: { lab_id: labId, test_type: 'test_case' },
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            $('#test_id').empty().append('<option value="" disabled>{{ __("messages.select_test") }}</option>');
                            $.each(response.data, function(key, value) {
                                $('#test_id').append('<option value="' + value.id + '">' + value.name + (value.price ? ' - ' + currencyFormat(value.price) : '') + '</option>');
                            });
                            $('#test_id').trigger('change'); // Re-initialize Select2
                        } else {
                            $('#test_id').empty().append('<option value="" disabled>{{ __("messages.no_test_availabile") }}</option>').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error('{{ __("messages.error_loading_tests") }}', xhr);
                    }
                });
            } else {
                $('#test_id').empty().append('<option value="" disabled>{{ __("messages.select_test") }}</option>').hide();
            }
        }

        function loadPackages() {
            let labId = $('#lab_id').val();
            if (labId) {
                $.ajax({
                    url: "{{ route('backend.catlogmanagements.test_list') }}",
                    type: "GET",
                    data: { lab_id: labId, test_type: 'test_package' },
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            $('#package_id').empty().append('<option value="" disabled>{{ __("messages.select_package") }}</option>');
                            $.each(response.data, function(key, value) {
                                $('#package_id').append('<option value="' + value.id + '">' + value.name + (value.price ? ' - ' + currencyFormat(value.price) : '') + '</option>');
                            });
                            $('#package_id').trigger('change'); // Re-initialize Select2
                        } else {
                            $('#package_id').empty().append('<option value="" disabled>{{ __("messages.no_packages_availabile") }}</option>').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error('{{ __("messages.error_loading_packages") }}', xhr);
                    }
                });
            } else {
                $('#package_id').empty().append('<option value="" disabled>{{ __("messages.select_package") }}</option>').hide();
            }


          
        }
    </script>
@endpush