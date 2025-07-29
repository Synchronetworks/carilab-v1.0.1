@extends('backend.layouts.app')
@section('title'){{__($module_title)}}@endsection
@section('content')
<div class="form-content">
    {{ html()->form('POST' ,route('backend.plans.store'))
    ->attribute('data-toggle', 'validator')
    ->attribute('id', 'form-submit')  // Add the id attribute here
    ->class('requires-validation')  // Add the requires-validation class
    ->attribute('novalidate', 'novalidate')  // Disable default browser validation
    ->open()
    }}

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
            </div>
            <a href="{{ route('backend.plans.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_plan_name') . ' <span class="text-danger">*</span>', 'name')->class('form-label') }}
                        {{ html()->text('name')
                                    ->attribute('value', old('name'))  ->placeholder(__('messages.placeholder_plan_name'))
                                    ->class('form-control')
                                    ->attribute('required','required')
                                }}
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.plan_name_required')}}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_select_duration') . '<span class="text-danger">*</span>', 'duration')->class('form-label') }}
                            {{
                                html()->select('duration', [
                                        '' => __('messages.lbl_select_duration'),
                                        'week' => __('messages.week'),
                                        'month' => __('messages.month'),
                                        'year' => __('messages.year')
                                    ], old('duration'))
                                    ->class('form-select select2')
                                    ->id('duration')
                                    ->attribute('placeholder', __('message.lbl_plan_duration_type'))
                                    ->attribute('required','required')
                            }}
                        @error('duration')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.duration_required')}}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_plan_duration_value') . '<span class="text-danger">*</span>', 'duration_value')->class('form-label') }}
                        {{
                                html()->input('number', 'duration_value', old('duration_value'))
                                    ->class('form-control')
                                    ->id('duration_value')
                                    ->attribute('placeholder', __('messages.placeholder_plan_duration_type'))
                                    ->attribute('oninput', "this.value = Math.abs(this.value)")
                                    ->attribute('required','required')
                            }}
                        @error('duration_value')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.duration_value_required')}}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_plan_amount') . '<span class="text-danger">*</span>', 'price')->class('form-label') }}
                        {{
                            html()->input('number', 'price', old('price'))
                                ->class('form-control')
                                ->id('price')
                                ->attribute('step', '0.01')
                                ->attribute('placeholder', __('messages.placeholder_plan_amount'))
                                ->attribute('oninput', "this.value = Math.abs(this.value)")
                                ->attribute('required','required')
                        }}
                        @error('price')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.plan_amount_required')}}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_plan_discount'), 'discount')->class('form-label') }}
                        <div class="d-flex align-items-center justify-content-between form-control">
                            {{ html()->label(__('messages.active'), 'discount')->class('form-label mb-0 text-body') }}
                            <div class="form-check form-switch">
                                {{ html()->hidden('discount', 0) }}
                                {{
                                    html()->checkbox('discount',old('discount', false))
                                        ->class('form-check-input')
                                        ->id('discount-toggle')
                                }}
                            </div>
                            @error('discount')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    @if($purchaseMethodEnabled)
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_android_identifier') . '<span class="text-danger">*</span>', 'android_identifier')->class('form-label') }}
                            {{
                                html()->text('android_identifier', old('android_identifier'))
                                    ->class('form-control')
                                    ->id('android_identifier')
                                    ->attribute('placeholder', __('messages.lbl_android_identifier'))
                                    ->attribute('required','required')
                            }}
                        @error('android_identifier')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="android_identifier-error">{{__('messages.android_indentifier_required')}}</div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_apple_identifier') . '<span class="text-danger">*</span>', 'apple_identifier')->class('form-label') }}
                            {{
                                html()->text('apple_identifier', old('apple_identifier'))
                                    ->class('form-control')
                                    ->id('apple_identifier')
                                    ->attribute('placeholder', __('messages.lbl_apple_identifier'))
                                    ->attribute('required','required')
                            }}
                        @error('apple_identifier')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="apple_identifier-error">{{__('messages.apple_indentifier_required')}}</div>
                    </div>
                    @endif
                            
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                        <div class="d-flex align-items-center justify-content-between form-control">
                            {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                            <div class="form-check form-switch">
                                {{ html()->hidden('status', 1) }}
                                {{
                                    html()->checkbox('status',old('status', true))
                                        ->class('form-check-input')
                                        ->id('status')
                                }}
                            </div>
                            @error('status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Percentage (shown when discount is enabled) -->
                    <div class="col-md-6 col-lg-4 d-none" id="discountPercentageSection">
                        {{ html()->label(__('messages.lbl_discount_percentage') . '<span class="text-danger">*</span>', 'discount_percentage')->class('form-label') }}
                        {{
                            html()->input('number', 'discount_percentage', old('discount_percentage'))
                                ->class('form-control')
                                ->id('discount_percentage')
                                ->attribute('min', '0')
                                ->attribute('max', '99')
                                ->attribute('step', '0.01')
                                ->attribute('pattern', '^\d*\.?\d{0,2}$') 
                                ->attribute('placeholder', __('messages.enter_discount_percentage'))
                        }}
                        @error('discount_percentage')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="discount-error">{{ __('messages.discount_percentage_is_required') }}</div>
                        <div class="invalid-feedback" id="discount-max-error" style="display: none;"> {{ __('messages.discount_percentage_cannot_exceed_99') }}</div>
                    </div>

                    <!-- Total Price (automatically calculated) -->
                    <div class="col-md-6 col-lg-4 d-none" id="totalPriceSection">
                        {{ html()->label(__('messages.lbl_total_amount'), 'total_price')->class('form-label') }}
                        {{
                            html()->input('number', 'total_price', old('total_price'))
                                ->class('form-control')
                                ->id('total_price')
                                ->attribute('step', '0.01')
                                ->attribute('placeholder', __('messages.lbl_total_amount'))
                                ->attribute('readonly', 'readonly')
                        }}
                        @error('total_price')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="total-price-error">{{__('messages.total_price_required')}}</div>
                    </div>
                    
                    <div class="col-md-12">
                        {{ html()->label(__('messages.lbl_description') . '<span class="text-danger">*</span>', 'description')->class('form-label') }}
                        {{ html()->textarea('description')
                                    ->attribute('value', old('description'))  ->placeholder(__('messages.lbl_plan_limit_description'))
                                    ->class('form-control')
                                    ->attribute('required','required')
                                }}
                        @error('description')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="desc-error">{{__('messages.description_required')}}</div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($planLimits))
            <div class="card-input-title mb-3">
                <h5>{{ __('messages.lbl_plan_limits') }}</h5>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="row gy-3">
                    @foreach($planLimits as $planLimit)
                        <div class="col-md-6">
                            <label for="{{ $planLimit->slug }}" class="form-label">{{ $planLimit->title }}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="{{ $planLimit->slug }}" class="form-label mb-0 text-body">{{ __('messages.lbl_on') }}</label>
                                <div class="form-check form-switch ">
                                    <input type="hidden" name="limits[{{ $planLimit->id }}][planlimitation_id]" value="{{ $planLimit->id }}">
                                    <input type="hidden" name="limits[{{ $planLimit->id }}][limitation_slug]" value="{{ $planLimit->slug }}">
                                    <input type="hidden" name="limits[{{ $planLimit->id }}][value]" value="0">
                                    <input type="checkbox" name="limits[{{ $planLimit->id }}][value]" id="{{ $planLimit->slug }}" class="form-check-input" value="1" {{ old($planLimit->slug, false) ? 'checked' : '' }}>
                                </div>
                                @error($planLimit->slug )
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- New Laboratory Limit -->
                        @if($planLimit->slug == 'number-of-laboratories')
                            <div class="col-md-6 d-none" id="laboratoryLimitInput">
                                {{ html()->label(__('messages.lbl_number_of_laboratories'), 'laboratory_limit_value')->class('form-label') }}
                                {{
                                    html()->input('number', 'laboratory_limit_value', old('laboratory_limit_value', 1))
                                        ->class('form-control')
                                        ->id('laboratory_limit_value')
                                        ->attribute('placeholder', __('messages.lbl_number_of_laboratories'))
                                        ->attribute('min', '1')
                                }}
                                @error('laboratory_limit_value')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- New Collector Limit -->
                        @if($planLimit->slug == 'number-of-collectors')
                            <div class="col-md-6 d-none" id="collectorLimitInput">
                                {{ html()->label(__('messages.lbl_number_of_collectors'), 'collector_limit_value')->class('form-label') }}
                                {{
                                    html()->input('number', 'collector_limit_value', old('collector_limit_value', 3))
                                        ->class('form-control')
                                        ->id('collector_limit_value')
                                        ->attribute('placeholder', __('messages.lbl_number_of_collectors'))
                                        ->attribute('min', '1')
                                }}
                                @error('collector_limit_value')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        @if($planLimit->slug == 'number-of-test-case')
                            <div class="col-md-6 d-none" id="testcaseLimitInput">
                                {{ html()->label(__('messages.lbl_number_of_test_cases'), 'test_case_limit_value')->class('form-label') }}
                                {{
                                    html()->input('number', 'test_case_limit_value', old('test_case_limit_value', 20))
                                        ->class('form-control')
                                        ->id('test_case_limit_value')
                                        ->attribute('placeholder', __('messages.lbl_number_of_test_cases'))
                                        ->attribute('min', '1')
                                }}
                                @error('test_case_limit_value')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                 
                        @if($planLimit->slug == 'number-of-test-package')
                            <div class="col-md-6 d-none" id="testpackageLimitInput">
                                {{ html()->label(__('messages.lbl_number_of_test_packages'), 'test_package_limit_value')->class('form-label') }}
                                {{
                                    html()->input('number', 'test_package_limit_value', old('test_package_limit_value', 20))
                                        ->class('form-control')
                                        ->id('test_package_limit_value')
                                        ->attribute('placeholder', __('messages.lbl_number_of_test_packages'))
                                        ->attribute('min', '1')
                                }}
                                @error('test_package_limit_value')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                    @endforeach
                </div>
            </div>
        </div>


        <div class="d-flex justify-content-end gap-3">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-right')->id('submit-button') }}
        </div>
    {{ html()->form()->close() }}
</div>

@endsection
@push('after-scripts')
      <script>

          tinymce.init({
            selector: '#description',
            plugins: 'link image code',
            toolbar: 'undo redo | styleselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | removeformat | code | image',
            setup: function(editor) {
                // Setup TinyMCE to listen for changes
                editor.on('change', function(e) {
                    // Get the editor content
                    const content = editor.getContent().trim();
                    const $textarea = $('#description');
                    const $error = $('#desc-error');
                      
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
        $(document).on('click', '.variable_button', function() {
            const textarea = $(document).find('.tab-pane.active');
            const textareaID = textarea.find('textarea').attr('id');
            tinyMCE.activeEditor.selection.setContent($(this).attr('data-value'));
        });

        document.addEventListener('DOMContentLoaded', function () {
            const discountToggle = document.getElementById('discount-toggle');
            const discountPercentageSection = document.getElementById('discountPercentageSection');
            const totalPriceSection = document.getElementById('totalPriceSection');
            const priceInput = document.getElementById('price');
            const discountPercentageInput = document.getElementById('discount_percentage');
            const totalPriceInput = document.getElementById('total_price');
            const discountError = document.getElementById('discount-error');
            const discountMaxError = document.getElementById('discount-max-error');
            const submitButton = document.getElementById('submit-button');
            const form = document.querySelector('form'); 

            function toggleDiscountFields() {
                if (discountToggle.checked) {
                    discountPercentageSection.classList.remove('d-none');
                    totalPriceSection.classList.remove('d-none');
                    discountPercentageInput.setAttribute('required', 'required');
                } else {
                    discountPercentageSection.classList.add('d-none');
                    totalPriceSection.classList.add('d-none');
                    discountPercentageInput.removeAttribute('required');
                    discountPercentageInput.value = ''; // Clear the discount percentage input
                    totalPriceInput.value = priceInput.value; // Set total price to the original price
                    hideValidationErrors();
                }
            }

            function calculateTotalPrice() {
                const price = parseFloat(priceInput.value) || 0;
                const discountPercentage = parseFloat(discountPercentageInput.value);

                hideValidationErrors(); // Clear validation errors before checking

                if (discountToggle.checked) {
                    // Check if discount percentage is empty
                    if (isNaN(discountPercentageInput.value) || discountPercentageInput.value.trim() === '') {
                        discountError.style.display = 'block'; // Show required error
                        return false; // Prevent further calculation and return false
                    }

                    // Check for negative or invalid discount percentage
                    if (discountPercentage < 0 || discountPercentage > 99) {
                        discountMaxError.style.display = 'block'; // Show validation error for exceeding limit or negative values
                        return false; // Prevent further calculation and return false
                    }

                    // Calculate the total price if discount is valid
                    if (discountPercentage >= 0 && discountPercentage <= 99) {
                        const discountAmount = price * (discountPercentage / 100);
                        const totalPrice = price - discountAmount;
                        totalPriceInput.value = totalPrice.toFixed(2);
                    } else {
                        totalPriceInput.value = price.toFixed(2);
                    }
                } else {
                    totalPriceInput.value = price.toFixed(2);
                }

                return true; // Valid case returns true
            }

            function hideValidationErrors() {
                discountError.style.display = 'none';
                discountMaxError.style.display = 'none';
            }

            // Add validation on submit button click
            submitButton.addEventListener('click', function (event) {
                const isValid = calculateTotalPrice();

                if (!isValid) {
                    event.preventDefault(); // Prevent form submission if validation fails
                }
            });

            discountToggle.addEventListener('change', toggleDiscountFields);
            discountPercentageInput.addEventListener('input', calculateTotalPrice);
            priceInput.addEventListener('input', calculateTotalPrice);

            toggleDiscountFields();
        });

        function toggleLimitFields(slug, isChecked) {
        let limitFields = {
            'number-of-laboratories': '#laboratoryLimitInput',
            'number-of-collectors': '#collectorLimitInput',
            'number-of-test-case': '#testcaseLimitInput',
            'number-of-test-package': '#testpackageLimitInput',
        };

        // Select the field by its slug and map it to the limitFields object
        const field = document.querySelector(limitFields[slug]);

        if (field) {
            // Show or hide the field based on the checkbox state
            if (isChecked) {
                field.classList.remove('d-none');
            } else {
                field.classList.add('d-none');
            }
        }
    }

    // Add event listeners to each checkbox
    document.addEventListener('DOMContentLoaded', function() {
        // Get all checkboxes related to the limits
        const checkboxes = document.querySelectorAll('.form-check-input');

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                toggleLimitFields(checkbox.id, checkbox.checked);
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
    // Get all limit input fields
    const limitInputs = {
        'laboratory_limit_value': "{{ __('messages.number_of_laboratories') }}",
        'collector_limit_value': "{{ __('messages.number_of_collectors') }}",
        'test_case_limit_value': "{{ __('messages.number_of_test_cases') }}",
        'test_package_limit_value': "{{ __('messages.number_of_test_packages') }}",
        
    };

    // Add validation for each limit input
    Object.keys(limitInputs).forEach(inputId => {
        const input = document.getElementById(inputId);
        const checkbox = document.getElementById(limitInputs[inputId]);
        
        if (input && checkbox) {
            input.addEventListener('focusout', function() {
                validateLimitInput(input, checkbox);
            });

            // Also validate on checkbox change
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    validateLimitInput(input, checkbox);
                } else {
                    // Reset validation state when unchecked
                    input.classList.remove('is-invalid');
                    const errorDiv = input.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv.style.display = 'none';
                    }
                }
            });
        }
    });

    function validateLimitInput(input, checkbox) {
        if (checkbox.checked) {
            const value = parseInt(input.value);
            if (isNaN(value) || value < 1) {
                input.classList.add('is-invalid');
                // Create error message if it doesn't exist
                let errorDiv = input.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = "{{ __('messages.minimum_value_of_1_is_required') }}";
                    input.parentNode.insertBefore(errorDiv, input.nextSibling);
                }
                errorDiv.style.display = 'block';
            } else {
                input.classList.remove('is-invalid');
                const errorDiv = input.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.style.display = 'none';
                }
            }
        }
    }
});
       

   </script>
@endpush


