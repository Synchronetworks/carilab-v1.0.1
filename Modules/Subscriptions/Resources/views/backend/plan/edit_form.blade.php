@extends('backend.layouts.app')
@section('title'){{__($module_title)}}@endsection
@section('content')
<div class="form-content">
    {{ html()->form('PUT' ,route('backend.plans.update', $data->id))
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
                <div class="row gy-4">
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_plan_name') . ' <span class="text-danger">*</span>', 'name')->class('form-label') }}
                        {{ html()->text('name')
                                    ->attribute('value', $data->name)  ->placeholder(__('messages.placeholder_plan_name'))
                                    ->class('form-control')
                                    ->attribute('required','required')
                                }}
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.plan_name_required')}}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_level') . '<span class="text-danger">*</span>', 'level')->class('form-label') }}
                        {{
                        html()->select('level',
                            isset($plan) && $plan > 0
                                ? collect(range(1, $plan + 1))->mapWithKeys(fn($i) => [$i => 'Level ' . $i])->prepend(__('Select Level'), '')->toArray()
                                : ['1' => 'Level 1'],
                            old('level', $data->level ?? '')
                        )->class('form-select select2')->id('level')->attribute('placeholder', __('messages.lbl_plan_level'))->attribute('required','required')
                        }}
                        @error('level')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.level_required')}}</div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_select_duration') . '<span class="text-danger">*</span>', 'duration')->class('form-label') }}
                        {{
                                    html()->select('duration', [
                                            '' => __('messages.lbl_select_duration'),
                                            'week' => 'Week',
                                            'month' => 'Month',
                                            'year' => 'Year'
                                        ], $data->duration)
                                        ->class('form-select select2')
                                        ->id('duration')
                                        ->attribute('placeholder', __('messages.lbl_plan_duration_type'))
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
                                html()->input('number', 'duration_value', $data->duration_value)
                                    ->class('form-control')
                                    ->id('duration_value')
                                    ->attribute('placeholder', __('messages.placeholder_plan_duration_type'))
                                    ->attribute('oninput', "this.value = Math.abs(this.value)")
                                    // ->attribute('min', '1')
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
                            html()->input('number', 'price', $data->price)
                                ->class('form-control')
                                ->id('price')
                                ->attribute('step', '1')
                                ->attribute('placeholder', __('messages.placeholder_plan_amount'))
                                ->attribute('oninput', "this.value = Math.abs(this.value)")
                                // ->attribute('min', '0')
                                ->attribute('required','required')
                        }}
                        @error('price')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.plan_amount_required')}}</div>
                    </div>
                    <!-- Discount Toggle -->
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_plan_discount'), 'discount')->class('form-label') }}
                        <div class="d-flex align-items-center justify-content-between form-control">
                            {{ html()->label(__('messages.active'), 'discount')->class('form-label mb-0 text-body') }}
                            <div class="form-check form-switch">
                                {{ html()->hidden('discount', 0) }}
                                {{
                                    html()->checkbox('discount', old('discount', $data->discount))
                                        ->class('form-check-input')
                                        ->id('discount-toggle')
                                }}
                            </div>
                        </div>
                        @error('discount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                        @if($purchaseMethodEnabled)
                    <div class="col-md-6 col-lg-4">
                        {{ html()->label(__('messages.lbl_android_identifier') . '<span class="text-danger">*</span>', 'android_identifier')->class('form-label') }}
                            {{
                                html()->text('android_identifier', old('android_identifier', $data->android_identifier ?? ''))
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
                                html()->text('apple_identifier', old('apple_identifier', $data->apple_identifier ?? ''))
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
                        <div class="d-flex justify-content-between align-items-center form-control">
                            {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                            <div class="form-check form-switch">
                                {{ html()->hidden('status', 0) }}
                                {{
                                        html()->checkbox('status',$data->status )
                                            ->class('form-check-input')
                                            ->id('status')
                                    }}
                            </div>
                        </div>
                        @error('status')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 col-lg-4 discount-section {{ $data->discount ? '' : 'd-none' }}" id="discountPercentageSection">
                        {{ html()->label(__('messages.lbl_discount_percentage') . '<span class="text-danger">*</span>', 'discount_percentage')->class('form-label') }}
                        {{
                            html()->input('number', 'discount_percentage', old('discount_percentage', $data->discount_percentage ?? 0))
                                ->class('form-control')
                                ->id('discount_percentage')
                                ->attribute('min', '0') 
                                ->attribute('max', '99')
                                ->attribute('step', '0.01')
                                ->attribute('placeholder', __('messages.enter_discount_percentage'))
                        }}
                        @error('discount_percentage')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div id="discount-error" class="invalid-feedback" style="display: none;">Discount percentage is required</div>
                        <div id="discount-max-error" class="invalid-feedback" style="display: none;">Discount percentage cannot exceed 99%  and must be a positive number.</div>
                    </div>  
                    <div class="col-md-6 col-lg-4 discount-section {{ $data->discount ? '' : 'd-none' }}" id="totalPriceSection">
                        {{ html()->label(__('messages.lbl_total_amount'), 'total_price')->class('form-label') }}
                        {{
                            html()->input('number', 'total_price', number_format(old('total_price', $data->total_price), 2, '.', ''))
                                ->class('form-control')
                                ->id('total_price')
                                ->attribute('step', '0.01')
                                ->attribute('placeholder', __('plan.lbl_total_amount'))
                                ->attribute('readonly', 'readonly')
                        }}
                        @error('total_price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="total-price-error">{{__('messages.total_price_required')}}</div>
                    </div>                    
                    <div class="col-md-12">
                        {{ html()->label(__('messages.lbl_description') . '<span class="text-danger">*</span>', 'description')->class('form-label') }}
                        {{ html()->textarea('description', $data->description)
                                    ->placeholder(__('messages.lbl_plan_limit_description'))
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
        <div class="card-input-title mb-33">
            <h5>{{ __('messages.lbl_plan_limits') }}</h5>
        </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="row gy-4">
                    @foreach($planLimits as $planLimit)        
                        <div class="col-md-6">
                            <label for="{{ $planLimit->limitation_slug }}" class="form-label">{{ $planLimit->limitation_data->title }}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="{{ $planLimit->limitation_slug }}" class="form-label mb-0 text-body">{{ __('messages.lbl_on') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="limits[{{ $planLimit->id }}][planlimitation_id]" value="{{ $planLimit->planlimitation_id }}">
                                    <input type="hidden" name="limits[{{ $planLimit->id }}][limitation_slug]" value="{{ $planLimit->limitation_slug }}">
                                    <input type="hidden" name="limits[{{ $planLimit->id }}][value]" value="0">
                                    <input type="checkbox" name="limits[{{ $planLimit->id }}][value]" id="{{ $planLimit->limitation_slug }}" class="form-check-input" value="1" {{ old('limits.' . $planLimit->id . '.value', $planLimit->limitation_value) ? 'checked' : '' }}>
                                </div>
                                @error($planLimit->slug)
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Conditional Fields for Specific Plan Limits -->
                            @if($planLimit->limitation_slug == 'number-of-laboratories')
            
                            <div class="col-md-6 " id="laboratoryLimitInput">
                                {{ html()->label(__('messages.lbl_number_of_laboratories'), 'laboratory_limit_value')->class('form-label') }}
                                {{
                                    html()->input('number', 'laboratory_limit_value', old('laboratory_limit_value', $planLimit->limit))
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

                            @if($planLimit->limitation_slug == 'number-of-collectors')
                            <div class="col-md-6 " id="collectorLimitInput">
                                {{ html()->label(__('messages.lbl_number_of_collectors'), 'collector_limit_value')->class('form-label') }}
                                {{
                                    html()->input('number', 'collector_limit_value', old('collector_limit_value', $planLimit->limit))
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

                            @if($planLimit->limitation_slug == 'number-of-test-case')
                                <div class="col-md-6 " id="testcaseLimitInput">
                                    {{ html()->label(__('messages.lbl_number_of_test_cases'), 'test_case_limit_value')->class('form-label') }}
                                    {{
                                        html()->input('number', 'test_case_limit_value', old('test_case_limit_value', $planLimit->limit))
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
                            @if($planLimit->limitation_slug == 'number-of-test-package')
                                <div class="col-md-6 " id="testpackageLimitInput">
                                    {{ html()->label(__('messages.lbl_number_of_test_packages'), 'test_package_limit_value')->class('form-label') }}
                                    {{
                                        html()->input('number', 'test_package_limit_value', old('test_package_limit_value', $planLimit->limit))
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

        $(document).ready(function() {
            const $discountToggle = $('#discount-toggle');
            const $discountPercentageSection = $('#discountPercentageSection');
            const $totalPriceSection = $('#totalPriceSection');
            const $discountPercentageInput = $('#discount_percentage');
            const $priceInput = $('#price');
            const $totalPriceInput = $('#total_price');
            const $form = $('#form-submit'); 
            const $discountError = $('#discount-error'); // Error for invalid percentage
            const $discountMaxError = $('#discount-max-error'); // Error for max percentage

            function updateSections() {
                const price = parseFloat($priceInput.val()) || 0;

                if ($discountToggle.is(':checked')) {
                    $discountPercentageSection.removeClass('d-none');
                    $totalPriceSection.removeClass('d-none');
                    $discountPercentageInput.prop('required', true);
                } else {
                    $discountPercentageSection.addClass('d-none');
                    $totalPriceSection.addClass('d-none');
                    $discountPercentageInput.prop('required', false);

                    $discountPercentageInput.val(0);  // Set discount to 0 when off
                    $totalPriceInput.val(price.toFixed(2)); // Reset total price to match price when discount is off
                }
            }

            $discountToggle.change(updateSections);
            updateSections();

            $discountPercentageInput.on('input', function() {
                const price = parseFloat($priceInput.val()) || 0;
                let discountPercentage = parseFloat($(this).val()) || 0;

                // Prevent negative input
                if (discountPercentage < 0) {
                    $(this).val(0); 
                    discountPercentage = 0; 
                }

                // Check if discount exceeds 99%
                if (discountPercentage > 99) {
                    discountPercentage = 0; 
                    $(this).val(discountPercentage); 
                    $discountMaxError.show(); 
                } else {
                    $discountMaxError.hide(); 
                }

                // Validate if discount percentage is empty or less than 1
                if (discountPercentage < 1 && discountPercentage > 0) {
                    $(this).addClass('is-invalid'); 
                    $discountError.show(); 
                } else {
                    $(this).removeClass('is-invalid'); // Remove invalid class if valid
                    $discountError.hide(); // Hide validation message
                }

                const discountAmount = (price * discountPercentage) / 100;
                const totalPrice = price - discountAmount;
                $totalPriceInput.val(totalPrice.toFixed(2));
            });

            $form.on('submit', function(e) {
                // Check if discount is active and percentage is empty
                if ($discountToggle.is(':checked') && !$discountPercentageInput.val()) {
                    e.preventDefault(); 
                    $discountError.show(); 
                }
            });

            // Handle price input change to recalculate total price if discount is active
            $priceInput.on('input', function() {
                const price = parseFloat($(this).val()) || 0;
                const discountPercentage = parseFloat($discountPercentageInput.val()) || 0;
                const discountAmount = (price * discountPercentage) / 100;
                const totalPrice = $discountToggle.is(':checked') ? (price - discountAmount) : price;
                $totalPriceInput.val(totalPrice.toFixed(2));
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
    // Get all checkboxes related to the limits
    const checkboxes = document.querySelectorAll('.form-check-input');
    
    // Function to toggle visibility of fields based on the checkbox
    function toggleLimitFields(slug, isChecked) {
        const limitFields = {
            'number-of-laboratories': '#laboratoryLimitInput',
            'number-of-collectors': '#collectorLimitInput',
            'number-of-test-case': '#testcaseLimitInput',
            'number-of-test-package': '#testpackageLimitInput',
        };

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

    checkboxes.forEach(function(checkbox) {
        // Initialize visibility based on checkbox state
        toggleLimitFields(checkbox.id, checkbox.checked);

        checkbox.addEventListener('change', function() {
            toggleLimitFields(checkbox.id, checkbox.checked);
        });
    });
});
   
         
       
   </script>
@endpush
