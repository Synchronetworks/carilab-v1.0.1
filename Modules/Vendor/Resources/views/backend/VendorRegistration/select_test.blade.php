<div class="card">
    <div class="card-body">
        <form id="step-form" method="POST" enctype="multipart/form-data" class="requires-validation" novalidate>
            @csrf
            
            <!-- Hidden fields for unchecked values -->
            <input type="hidden" name="status" value="0">
            <input type="hidden" name="is_home_collection_available" value="0">
            <input type="hidden" name="is_featured" value="0">
            <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $vendor_id }}">
      
            <!-- Basic Information -->
            <div class="row gy-3 mb-4">
                <h4 class="mb-0">{{ __('messages.basic_information') }}</h4>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.test_case_image') }}</label>
                        <div class="btn-file-upload">      
                        <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                            <img id="imagePreview"
                                src="{{ $data->profile_image ?? asset('images/default-logo.png') }}"
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
                        <input type="file" name="test_image" id="testImageInput" class="form-control d-none" accept="image/png,image/jpeg">
                        @error('test_image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row gy-4">
                        <div class="col-lg-6 ">
                            <label class="form-label">{{ __('messages.test_name') }}<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{ __('messages.enter_test_name') }}" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.test_name_required') }}</div>
                        </div>

                        <div class="col-lg-6 ">
                            <label class="form-label">{{ __('messages.test_code') }}<span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="{{ __('messages.enter_test_code') }}" required>
                            @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.test_code_required') }}</div>
                        </div>

                        <div class="col-lg-6 ">
                            <label class="form-label">{{ __('messages.lbl_test_type') }}<span class="text-danger">*</span></label>
                            <select class="form-select select2" name="type[]" id="test_type" multiple required data-placeholder="{{ __('messages.select_test_type') }}">
                                <option value="">{{ __('messages.select_type') }}</option>
                                @foreach($test_types as $test_type)
                                    <option value="{{ $test_type->value }}">{{ $test_type->value }}</option>
                                @endforeach
                            
                            </select>
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.test_type_required') }}</div>
                        </div>

                        <div class="col-lg-6 ">
                            <label class="form-label">{{ __('messages.required_equipment') }}<span class="text-danger">*</span></label>
                            <select class="form-select select2" name="equipment[]" id="equipment" multiple required data-placeholder="{{ __('messages.required_equipment') }}">
                                <option value="">{{ __('messages.required_equipment') }}</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->value }}">{{ $equipment->value }}</option>
                                @endforeach
                                
                            </select>
                            @error('equipment') <span class="text-danger">{{ $message }}</span> @enderror
                            @error('equipment.*') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.equipment_required') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <label class="form-label">{{ __('messages.lbl_description') }}</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="{{ __('messages.enter_description') }}">{{ old('description') }}</textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.description_required') }}</div>
                </div>
            </div>

            <!-- Test Details -->
            <div class="row gy-4 mb-4">
                <h4 class="mb-0">{{ __('messages.test_details') }}</h4>

                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.lbl_category') }}<span class="text-danger">*</span></label>
                    <select class="form-select select2" name="category_id" id="category_id" required>
                        <option value="">{{ __('messages.select_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.category_required') }}</div>
                </div>

            
                <input type="hidden" name="lab_id" id="lab_id" value="{{ $selectedLabId }}">
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.lbl_lab') }}<span class="text-danger">*</span></label>
                    <select class="form-select select2" name="lab_id" id="lab_id" required disabled>
                        <option value="">{{ __('messages.select_lab') }}</option>
                        @foreach($labs as $lab)
                            <option value="{{ $lab->id }}" {{ old('lab_id', $selectedLabId) == $lab->id ? 'selected' : '' }}>
                                {{ $lab->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('lab_id') 
                        <span class="text-danger">{{ $message }}</span> 
                    @enderror
                    <div class="invalid-feedback">{{ __('messages.lab_required') }}</div>
                </div>

                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.lbl_price') }}<span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" placeholder="{{ __('messages.enter_price') }}" required>
                    @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.price_required') }}</div>
                </div>

                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.lbl_duration') }} (in minutes)<span class="text-danger">*</span></label>
                    <input type="time" name="duration" class="form-control min-datetimepicker-time" value="{{ old('duration') }}" placeholder="{{ __('messages.enter_duration') }}" placeholder="{{ __('messages.enter_duartion') }}" required>
                    @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.duration_required') }}</div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.test_report_time') }}<span class="text-danger">*</span></label>
                    <input type="time" name="test_report_time" class="form-control min-datetimepicker-time" value="{{ old('test_report_time') }}" placeholder="{{ __('messages.enter_test_report_time') }}" required>
                    @error('test_report_time') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.test_report_time_required') }}</div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="row gy-3 mb-4">
                <h4 class="mb-0">{{ __('messages.additional_information') }}</h4>

                <div class="col-lg-12">
                    <label class="form-label">{{ __('messages.test_instruction') }}</label>
                    <textarea name="instructions" id="instructions" class="form-control" rows="3" placeholder="{{ __('messages.enter_instruction') }}">{{ old('instructions') }}</textarea>
                    @error('instructions') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.test_instructions_required') }}</div>
                </div>

                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.test_guidelines_pdf') }}</label>
                    <input type="file" name="guidelines_pdf" class="form-control"  accept=".pdf, image/*">
                    @error('guidelines_pdf') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.test_guidelines_pdf_required') }}</div>
                </div>

                <div class="col-lg-12">
                    <label class="form-label">{{ __('messages.additional_notes') }}</label>
                    <textarea name="additional_notes"  id="additional_notes" class="form-control" rows="3" placeholder="{{ __('messages.enter_additinal_notes') }}">{{ old('additional_notes') }}</textarea>
                    @error('additional_notes') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.additional_notes_required') }}</div>
                </div>

                <div class="col-lg-12">
                    <label class="form-label">{{ __('messages.test_restriction') }}</label>
                    <textarea name="restrictions" id="restrictions" class="form-control" rows="3" placeholder="{{ __('messages.enter_restriction') }}">{{ old('restrictions') }}</textarea>
                    @error('restrictions') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.test_restriction_required') }}</div>
                </div>
            </div>

           

            <div class="row">
                <div class="col-md-12">
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
$(document).ready(function() {
    // Logo Image Handling
    const $uploadButton = $('#uploadButton');
    const $removeButton = $('#removeButton');
    const $testImageInput = $('#testImageInput');
    const $imagePreview = $('#imagePreview');

    // Upload Button Click
    $uploadButton.on('click', function() {
        $testImageInput.trigger('click');
    });

    // Image Preview
    $testImageInput.on('change', function() {
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
        $testImageInput.val('');
        $imagePreview.attr('src', '{{ asset("images/default-logo.png") }}');
        $(this).hide();
    });
        
    // Initialize Select2 with tags enabled
    $('.select2').select2({
        tags: true,
        createTag: function(params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            }
        }
    });

    function saveNewOption(selectElement, type) {
        $(selectElement).on('select2:select', function(e) {
            if (e.params.data.newOption) {
                const newValue = e.params.data.text;
                
                $.ajax({
                    url: "{{ route('backend.constants.store') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: newValue,
                        type: type,
                        value: newValue,
                        sequence: 0,
                        status: 1
                    },
                    success: function(response) {
                        if (response.success) {
                            const $option = $(selectElement).find(`option[value="new:${newValue}"]`);
                            $option.val(response.data.id); // Update option value with DB ID
                            toastr.success('New ' + type + ' added successfully');
                        } else {
                            toastr.error('Failed to add new ' + type);
                            $(selectElement).find(`option[value="new:${newValue}"]`).remove();
                        }
                    },
                    error: function() {
                        toastr.error('Failed to add new ' + type);
                        $(selectElement).find(`option[value="new:${newValue}"]`).remove();
                    }
                });
            }
        });
    }

    // Apply function to both select elements
    saveNewOption('#test_type', 'test_type');
    saveNewOption('#equipment', 'equipment');
});

</script>