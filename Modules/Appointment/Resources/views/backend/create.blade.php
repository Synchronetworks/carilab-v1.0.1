@extends('backend.layouts.app')
@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="form-content">
        <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
            <div class="price-count-label d-flex align-items-center gap-2">
                <a href="javascript:void()" aria-hidden="true" data-bs-toggle="modal" data-bs-target="#appliedTax"><i
                        class="ph ph-info"></i></a>
                <div>{{ __('messages.total_appointment_amount') }} <span
                        id="total_amount">{{ Currency::format($subscription->total_amount ?? 0) }}</span></div>
            </div>
            <a href="{{ route('backend.appointments.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <form action="{{ route('backend.appointments.store') }}" method="POST" enctype="multipart/form-data"
            class="requires-validation" novalidate>
            @csrf
            <div class="row gy-4">
                <div class="col-md-12">
                    <div class="card-input-title mb-3">
                        <h4 class="m-0">{{ __('messages.appointment_details') }}</h4>
                    </div>
                    <div class="card">
                        <div class="card-body">

                            <div class="row gy-4">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label">{{ __('messages.lbl_customer') }}<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2" name="customer_id" id="customer_id" required>
                                        <option value="">{{ __('messages.select_customer') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->first_name }}
                                                {{ $customer->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.customer_field') }}</div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label">{{ __('messages.other_members') }}</label>
                                    <select class="form-select select2" name="other_member_id" id="other_members">
                                        <option value="" disabled>{{ __('messages.other_members') }}</option>

                                    </select>
                                    @error('other_members')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.other_member_field') }}</div>
                                </div>
                                @if (auth()->user()->user_type !== 'vendor' && multivendor() == 1)
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-label">{{ __('messages.lbl_vendor') }}<span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2" name="vendor_id" id="vendor_id" required>
                                            <option value="">{{ __('messages.select_vendor') }}</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->first_name }}
                                                    {{ $vendor->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <div class="invalid-feedback">{{ __('messages.vendor_required') }}</div>
                                    </div>
                                @else
                                    <input type="hidden" name="vendor_id" value="{{ auth()->id() }}">
                                @endif
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label">{{ __('messages.lab') }}<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2" name="lab_id" id="lab_id" required>
                                        <option value="">{{ __('messages.select_lab') }}</option>
                                    </select>
                                    @error('lab_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.lab_required') }}</div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label">{{ __('messages.lbl_test_type') }}<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2" name="test_type" id="test_type" required>
                                        <option value="" disabled selected>{{ __('messages.lbl_test_type') }}
                                        </option>
                                        <option value="test_case">{{ __('messages.lbl_test_case') }}</option>
                                        <option value="test_package">{{ __('messages.test_package') }}</option>
                                    </select>
                                    @error('test_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.test_type_required') }}</div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label">{{ __('messages.test_package_slash') }}<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2" name="test_id" id="test_id" required>
                                        <option value="">{{ __('messages.select_test') }}</option>
                                    </select>
                                    @error('test_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.test_required') }}</div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label">{{ __('messages.appointment_date') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="date" id="appointment_date" name="appointment_date"
                                        class="form-control datetimepicker" value="{{ old('appointment_date') }}"
                                        placeholder="{{ __('messages.select_date') }}" required>
                                    @error('appointment_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.appointment_date_required') }}</div>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">{{ __('messages.appointment_time') }}<span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <div class="avb-slot clickable-texts" id="available_slots">

                                        </div>
                                        <div id="slot_not_found" class="form-control">
                                            <h4 class="text-danger text-center m-0">
                                                {{ __('messages.slot_not_available') }}</h4>
                                        </div>
                                    </div>
                                    <input type="hidden" name="appointment_time" id="appointment_time" required>
                                    <div class="invalid-feedback">{{ __('messages.appointment_required') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-input-title mb-3">
                <h4 class="m-0">{{ __('messages.coupon_details') }}</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.lbl_coupon_code') }}</label>
                            <select class="form-select select2" name="coupon_id" id="coupon_id">
                                <option value="" disabled selected>{{ __('messages.lbl_select_coupon') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-input-title mb-3">
                <h4 class="m-0">{{ __('messages.other_details') }}</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.collection_type') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="collection_type" id="collection_type" required>
                                <option value="" disabled selected>{{ __('messages.lbl_select_collection') }}
                                </option>
                            </select>
                            @error('collection_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.collection_required') }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.medical_report') }}</label>
                            <input type="file" name="medical_report" class="form-control">
                            @error('medical_report')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.medical_report_required') }}</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.symptoms') }}</label>
                            <textarea name="symptoms" id="symptoms" class="form-control" rows="3">{{ old('symptoms') }}</textarea>
                            @error('symptoms')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.symptoms_field_required') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="submit" class="btn btn-primary">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
    <div class="modal fade" id="appliedTax" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h6 class="m-0">{{ __('messages.lbl_total_price') }} </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ph-circle-check text-primary font-size-140"></div>
                    <div class="mb-2 d-none d-flex justify-content-between" id="price_details_div">
                        <p class="m-0"><span class="heading-color">{{ __('messages.price_details') }}</span> </p>
                        <h6 class="m-0" id="price_details">0.00</h6>
                    </div>
                    <div id="discount_price_details" class="mb-2 d-none d-flex justify-content-between">
                        <p class="m-0"><span class="heading-color">{{ __('messages.lbl_discount_price') }}</span>
                            <span id="discount_type" class="text-success"></span>:</p>
                        <h6 class="m-0" id="discount_price">0.00</h6>
                    </div>
                    <div id="coupon_code_details" class="mb-2 d-none d-flex justify-content-between">
                        <p class="m-0"><span class="heading-color">{{ __('messages.coupon_discount') }}</span> (<span
                                id="coupon_code"></span>: <span id="coupon_value"></span>):</p>
                        <h6 class="m-0" id="coupon_amount">0.00</h6>
                    </div>

                    <div id="tax_details">
                    </div>
                    <div id="applied_tax">
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        function selectSlot(slot) {
            $('.clickable-text').removeClass('selected');
            $(`#slot_${slot.replace(':', '_')}`).addClass('selected');
            $('#appointment_time').val(slot);
        }

        $(document).ready(function() {

            $('#customer_id').on('change', function() {
                let customerId = $(this).val();
                if (customerId) {
                    $.ajax({
                        url: "{{ route('backend.customer.other_members') }}",
                        type: "GET",
                        data: {
                            customer_id: customerId
                        },
                        success: function(data) {
                            $('#other_members').empty();
                            $('#other_members').append(
                                '<option value="">{{ __('messages.other_members') }}</option>'
                                );
                            $.each(data, function(key, value) {
                                $('#other_members').append('<option value="' + value
                                    .id + '">' + value.first_name + ' ' + value
                                    .last_name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#other_members').empty();
                    $('#other_members').append(
                        '<option value="" disabled>{{ __('messages.other_members') }}</option>');
                }
            });


            let loggedInVendorId = "{{ auth()->user()->user_type == 'vendor' ? auth()->id() : null }}";

            function loadAllLabs(vendor_id = null) {
                $.ajax({
                    url: "{{ route('backend.labs.index_list') }}",
                    type: "GET",
                    data: {
                        vendor_id: vendor_id
                    },
                    success: function(data) {
                        labsList = data;
                        updateLabSelect(data);
                    },
                    error: function(xhr) {
                        console.error('{{ __('messages.error') }}', xhr);
                        resetLabSelect();
                    }
                });
            }

            if (loggedInVendorId) {
                loadAllLabs(loggedInVendorId);
            } else {
                loadAllLabs()
            };

            $('#vendor_id').on('change', function() {
                let vendorId = $(this).val();
                const $labSelect = $('#lab_id');

                if (vendorId) {
                    loadAllLabs(vendorId);
                } else {
                    loadAllLabs();
                }
            });


            // Lab change event
            $('#lab_id').on('change', function() {
                let vendor_id = $('#vendor_id').val();
                loadTests(vendor_id);
            });
            // Load available slots on page load

            $('#appointment_date').on('change', function() {
                let selectedLab = $('#lab_id').val();

                let selectedDate = $(this).val();

                loadAvailableSlots(selectedLab, selectedDate);
            });
            // Test type change event
            $('#test_type').on('change', function() {
                loadTests();
                loadAvailableCoupons();
            });
            // Other members select2
            $('#other_members').select2({
                placeholder: "{{ __('messages.other_members') }}",
                allowClear: true,
                width: '100%'
            });

            // Test change event
            $('#test_id').on('change', function() {
                loadAvailableCoupons();
                calculatePrices();
                changeCollectionType();
            });

            // Coupon change event
            $('#coupon_id').off('change').on('change', function() {
                calculatePrices();
            });

            // Load available slots on page load
            function loadAvailableSlots(labId, selectedDate) {
                $.ajax({
                    url: "{{ route('backend.labsession.available_slot') }}",
                    type: "GET",
                    data: {
                        lab_id: labId,
                        appointment_date: selectedDate
                    },
                    success: function(data) {
                        if (data.data && data.data.length > 0) {
                            let slotsHtml = '';
                            data.data.forEach(function(slot) {
                                slotsHtml +=
                                    `<label class="clickable-text" onclick="selectSlot('${slot}')" id="slot_${slot.replace(':', '_')}">${slot}</label>`;
                            });

                            $('#available_slots').html(slotsHtml);
                            $('#slot_not_found').addClass('d-none');
                        } else {
                            $('#available_slots').html('');
                            $('#slot_not_found').removeClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('{{ __('messages.error') }}', error);
                        $('#available_slots').html('');
                        $('#slot_not_found').removeClass('d-none');
                    }
                });
            }

            // Load available coupons   
            function loadAvailableCoupons() {
                const labId = $('#lab_id').val();
                const testType = $('#test_type').val();
                const customerId = $('#customer_id').val();
                let testId = '';


                if (testType === 'test_case') {
                    testId = $('#test_id').val();
                } else if (testType === 'test_package') {
                    testId = $('#test_id').val();
                }


                if (testType && testId) {
                    $.ajax({
                        url: "{{ route('backend.coupons.index_list') }}",
                        type: "GET",
                        data: {
                            lab_id: labId,
                            test_type: testType,
                            test_id: testId,
                            customer_id: customerId,
                        },
                        success: function(response) {

                            couponsList = response.coupons;
                            const $couponSelect = $('#coupon_id');


                            if ($couponSelect.hasClass('select2-hidden-accessible')) {
                                $couponSelect.select2('destroy');
                            }


                            $couponSelect.empty().append(
                                '<option value="">{{ __('messages.select_coupon') }}</option>');

                            if (couponsList && couponsList.length > 0) {
                                couponsList.forEach(function(coupon) {
                                    $couponSelect.append(
                                        `<option value="${coupon.coupon_code}">
                                    ${coupon.coupon_code} 
                                    (${coupon.discount_type === 'percentage' ? coupon.discount_value + '%' : currencyFormat(parseFloat(coupon.discount_value))})
                                </option>`
                                    );
                                });
                            }


                            $couponSelect.select2({
                                placeholder: "{{ __('messages.select_coupon') }}",
                                allowClear: true,
                                width: '100%'
                            });


                            $couponSelect.trigger('change');
                        },
                        error: function(xhr, status, error) {
                            console.error("{{ __('messages.error') }}", error);
                        }
                    });
                } else {

                    const $couponSelect = $('#coupon_id');


                    if ($couponSelect.hasClass('select2-hidden-accessible')) {
                        $couponSelect.select2('destroy');
                    }


                    $couponSelect.empty()
                        .append('<option value="">{{ __('messages.select_coupon') }}</option>')
                        .select2({
                            placeholder: "{{ __('messages.select_coupon') }}",
                            allowClear: true,
                            width: '100%'
                        })
                        .trigger('change');
                }
            }

            // Add this function to handle all price calculations
            function calculatePrices() {
                let selectedTestId = $('#test_id').val();
                let couponId = $('#coupon_id').val();
                let basePrice = 0;
                let discount = 0;
                let discount_value = '';
                let coupon_amount = 0;

                if (selectedTestId) {
                    let selectedTest = testsList.find(test => test.id == selectedTestId);
                    basePrice = selectedTest.price || 0;
                    window.originalBasePrice = basePrice;
                    $('#price_details_div').removeClass('d-none');
                    $('#price_details').text(currencyFormat(selectedTest.price));

                    if (selectedTest.is_discount == 1) {

                        $('#discount_price_details').removeClass('d-none');

                        if (selectedTest.discount_type == 'percentage') {
                            discount_value = selectedTest.discount_price + '%';
                            discount = (basePrice * selectedTest.discount_price) / 100 || 0;
                        } else {
                            discount_value = currencyFormat(selectedTest.discount_price);
                            discount = selectedTest.discount_price || 0;
                        }

                        if (discount > 0) {
                            basePrice = basePrice - discount;
                            window.discountedBasePrice = basePrice;
                        }
                    }


                    // Handle coupon if exists
                    if (couponId) {
                        const selectedCoupon = couponsList.find(coupon => coupon.coupon_code == couponId);
                        if (selectedCoupon) {
                            $('#coupon_code_details').removeClass('d-none');
                            $('#coupon_code').text(selectedCoupon.coupon_code);

                            if (selectedCoupon.discount_type == 'percentage') {
                                $('#coupon_value').text(selectedCoupon.discount_value + '%');
                                coupon_amount = (basePrice * selectedCoupon.discount_value) / 100 || 0;
                            } else if (selectedCoupon.discount_type == 'fixed') {
                                $('#coupon_value').text(currencyFormat(parseFloat(selectedCoupon.discount_value)));
                                coupon_amount = parseFloat(selectedCoupon.discount_value) || 0;
                            }

                            if (coupon_amount > 0) {
                                basePrice = parseFloat(basePrice) - parseFloat(coupon_amount);
                                $('#coupon_amount').text(currencyFormat(coupon_amount));
                            }
                        }
                    } else {
                        $('#coupon_code_details').addClass('d-none');
                        $('#coupon_amount').text(currencyFormat(0.00));
                    }

                    // Update all price displays
                    $('#discount_price').text(currencyFormat(discount));
                    $('#discount_type').text(discount_value);
                    $('#base_price').text(currencyFormat(basePrice));

                    // Update taxes and total
                    updateTaxAndTotal(basePrice);
                } else {
                    resetPriceDisplay();
                }
            }

            // Update lab select
            function updateLabSelect(data) {
                const $labSelect = $('#lab_id');
                $labSelect.empty();
                $labSelect.append('<option value="">{{ __('messages.select_lab') }}</option>');
                $.each(data, function(key, value) {
                    $labSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            }
            // Reset lab select
            function resetLabSelect() {
                const $labSelect = $('#lab_id');
                $labSelect.empty();
                $labSelect.append('<option value="">{{ __('messages.select_lab') }}</option>');

                // Reset related fields
                resetPriceDisplay();
                $('#test_id').empty().append('<option value="">{{ __('messages.select_test_case') }}</option>');
            }

            function changeCollectionType() {
                let testId = $('#test_id').val();
                let collectionTypeDropdown = $('#collection_type');

                collectionTypeDropdown.empty();

                if (testId) {

                    const selectedTest = testsList.find(test => test.id == testId);
                    if (selectedTest) {


                        collectionTypeDropdown.append(
                            '<option value="lab" selected>{{ __('messages.lab_collection') }}</option>');


                        if (selectedTest.is_home_collection_available === 1) {
                            collectionTypeDropdown.append(
                                '<option value="home">{{ __('messages.home_collection') }}</option>');
                        }
                    }
                } else {

                    collectionTypeDropdown.append(
                        '<option value="lab" selected>{{ __('messages.lab_collection') }}</option>');
                }


                collectionTypeDropdown.trigger('change');
            }
            // Load tests based on lab and test type
            function loadTests(vendor_id = null) {
                let labId = $('#lab_id').val();
                let testType = $('#test_type').val();

                if (testType) {
                    $.ajax({
                        url: "{{ route('backend.catlogmanagements.test_list') }}",
                        type: "GET",
                        data: {
                            vendor_id: vendor_id,
                            lab_id: labId,
                            test_type: testType
                        },
                        success: function(response) {
                            testsList = response.data;
                            let hasHomeCollection = false;
                            let selectLabel = (testType === 'test_package') ?
                                '{{ __('messages.select_test_package') }}' :
                                '{{ __('messages.select_case') }}';
                            $('#test_id').empty();
                            $('#test_id').append('<option value="" disabled selected>' + selectLabel +
                                '</option>');

                            if (response.data && response.data.length > 0) {
                                $.each(response.data, function(key, value) {
                                    $('#test_id').append('<option value="' + value.id + '">' +
                                        value.name +
                                        (value.price ? ' - ' + currencyFormat(value.price) :
                                            '') + '</option>');
                                    if (value.is_home_collection_available === 1) {
                                        hasHomeCollection = true;
                                    }
                                });
                            }
                            let collectionTypeDropdown = $('#collection_type');
                            collectionTypeDropdown.empty(); // Clear existing options

                            if (hasHomeCollection) {
                                collectionTypeDropdown.append(
                                    '<option value="home">{{ __('messages.home_collection') }}</option>'
                                    );
                            }

                            collectionTypeDropdown.append(
                                '<option value="lab" selected>{{ __('messages.lab_collection') }}</option>'
                                );

                            // Re-initialize select2 if you're using it
                            $('#test_id').trigger('change');
                            $('#collection_type').trigger('change');
                        },
                        error: function(xhr) {
                            console.error('Error loading tests:', xhr);
                        }
                    });
                } else {
                    // Clear test dropdown if no test type selected
                    $('#test_id').empty();
                    $('#test_id').append('<option value="">{{ __('messages.select_case') }}</option>');
                    $('#test_id').trigger('change');
                    resetPriceDisplay();

                    $('#collection_type').empty();
                    $('#collection_type').append(
                        '<option value="lab" selected>{{ __('messages.lab_collection') }}</option>');
                    $('#collection_type').trigger('change');
                }
            }

            // Reset price display
            function resetPriceDisplay() {
                $('#base_price').text(currencyFormat(0.00));
                $('#tax_details').html('');
                $('#total_amount').text(currencyFormat(0.00));
                $('#price_details_div').addClass('d-none');
                $('#price_details').text(currencyFormat(0.00));
                $('#discount_price_details').addClass('d-none')
                $('#discount_price').text(currencyFormat(0.00));
                $('#coupon_code_details').addClass('d-none');
                $('#coupon_code').text('');
                $('#coupon_value').text('');
                $('#coupon_amount').text(currencyFormat(0.00));
            }

            // Update taxes and total
            function updateTaxAndTotal(basePrice) {
                let labId = $('#lab_id').val();
                // Find the selected lab data
                const selectedLab = labsList.find(lab => lab.id == labId);
                let taxHtml = '';
                let totalTax = 0;
                if (selectedLab && selectedLab.tax_list && selectedLab.tax_list.length > 0 && basePrice > 0) {
                    selectedLab.tax_list.forEach(tax => {
                        if (tax.status == 1) {
                            let taxAmount = 0;

                            if (tax.type == 'Percentage') {

                                taxAmount = (basePrice * tax.value) / 100;
                                totalTax += taxAmount;
                            } else {
                                taxAmount = parseFloat(tax.value);
                                totalTax += taxAmount;
                            }

                            taxHtml += `
                        <div class="d-flex justify-content-between mb-1">
                            
                            <span>${tax.title} <span class="text-success">(${tax.type == 'Percentage' ? `${tax.value}%` : currencyFormat(tax.value)})</span></span>
                            <h6 class="m-0">${currencyFormat(taxAmount)}</h6>
                        </div>
                    `;
                        }
                    });

                    // Show tax details if there are any taxes
                    if (taxHtml) {
                        $('#tax_details').html(`
                    <div class="border-top pt-2 mb-2">
                        <h5>{{ __('messages.taxes') }}:</h5>
                        ${taxHtml}
                    </div>
                `);
                    } else {
                        var noApplicableTaxes = "{{ __('messages.no_applicable_taxes') }}";
                        $('#tax_details').html('<div class="text-muted">' + noApplicableTaxes + '</div>');
                    }

                    // Calculate and show total amount
                    const totalAmount = parseFloat(basePrice) + totalTax;

                    $('#total_amount').text(currencyFormat(totalAmount));
                } else {
                    const totalAmount = parseFloat(basePrice);

                    $('#total_amount').text(currencyFormat(totalAmount));
                   
                }
            }
        });
    </script>
@endpush
