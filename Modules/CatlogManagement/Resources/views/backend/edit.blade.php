@extends('backend.layouts.app')
@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@section('content')
<div class="form-content">
    <form action="{{ route('backend.catlogmanagements.update', $data->id) }}" method="POST" enctype="multipart/form-data" class="requires-validation" novalidate>
        @csrf
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="m-0">{{__('messages.basic_information')}}</h4>
            </div>
            <a href="{{ route('backend.catlogmanagements.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <!-- Basic Information -->
        <div class="card">
            <div class="card-body">                
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.test_case_image') }}</label>
                                <div class="btn-file-upload">
                                    <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                        <img id="imagePreview" 
                                                src="{{ $data->getTestImageAttribute() ?? asset('images/default-avatar.png') }}" 
                                                alt="Test Case Image" class="img-thumbnail avatar-150 object-cover">
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
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.test_name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="{{ __('messages.placeholder_test_name') }}" value="{{ old('name', $data->name) }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.test_name_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.test_code') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" placeholder="{{ __('messages.placeholder_test_code') }}" value="{{ old('code', $data->code) }}" required>
                                    @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.test_code_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.lbl_test_type') }}<span class="text-danger">*</span></label>
                                    <select class="form-select select2" name="type[]" id="test_type" multiple required>
                                        <option value="" disabled>{{ __('messages.select_test_type') }}</option>
                                        @php $selectedTypes = old('type', $data->type ?? []); @endphp
                                        @foreach($test_types as $test_type)
                                            <option value="{{ $test_type->value }}" {{ in_array($test_type->value, (array) $selectedTypes) ? 'selected' : '' }}>
                                                {{ $test_type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.test_type_required') }}</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.required_equipment') }}<span class="text-danger">*</span></label>
                                    <select class="form-select select2" name="equipment[]" id="equipment" multiple>
                                        <option value="" disabled>{{ __('messages.select_required_equipment') }}</option>
                                        @php $selectedEquipment = old('equipment', $data->equipment ?? []); @endphp
                                        @foreach($equipments as $equipment)
                                            <option value="{{ $equipment->value }}" {{ in_array($equipment->value, (array) $selectedEquipment) ? 'selected' : '' }}>
                                                {{ $equipment->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipment') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.equipment_required') }}</div>
                                </div>
                            </div>
                        </div>                       
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.description') }}</label>
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $data->description) }}</textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.description_max_length') }}</div>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Test Details -->
        <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.test_details') }}</h4>
        </div>
         <div class="card">
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.lbl_category') }}<span class="text-danger">*</span></label>
                        <select class="form-select select2" name="category_id" required>
                            <option value="" disabled>{{ __('messages.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $data->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.category_required') }}</div>
                    </div>
                        @if(multivendor()==1 && auth()->user()->user_type !== 'vendor')
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.lbl_vendor') }}<span class="text-danger">*</span></label>
                        <select class="form-select select2" name="vendor_id" id="vendor_id" required>
                            <option value="" disabled>{{ __('messages.select_vendor') }}</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id', $data->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->first_name }} {{ $vendor->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.vendor_required') }}</div>
                    </div>
                        @else
                        {{-- Hidden input for vendor users --}}
                    <input type="hidden" name="vendor_id" value="{{ auth()->id() }}">
                        @endif

                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.lbl_lab') }}<span class="text-danger">*</span></label>
                        <select class="form-select select2" name="lab_id" id="lab_id" required>
                            <option value="" {{ old('lab_id', $data->lab_id) == null ? 'selected' : '' }}>{{ __('messages.select_lab') }}</option>
                            @foreach($labs as $lab)
                                <option value="{{ $lab->id }}" {{ old('lab_id', $data->lab_id) == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('lab_id') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.select_lab') }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.lbl_price') }}({{getCurrencySymbol()}})<span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" min="0" step="any" placeholder="{{ __('messages.placeholder_price') }}" value="{{ old('price', $data->price) }}" required>
                        @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.price_required') }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.lbl_duration') }}<span class="text-danger">*</span></label>
                        <input type="time" class="form-control min-datetimepicker-time" name="duration" id="duration"  placeholder="{{ __('messages.placeholder_duration') }}"
                                value="{{ old('duration', $data->duration ? date('H:i', strtotime($data->duration)) : '') }}" required>
                    
                        <small class="text-muted">{{__('messages.format_hrs')}}</small>
                        @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.duration_required') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.test_report_time') }}<span class="text-danger">*</span></label>
                        <input type="time" class="form-control min-datetimepicker-time" name="test_report_time" id="test_report_time" placeholder="{{ __('messages.placeholder_report_time') }}"
                                value="{{ old('test_report_time', $data->test_report_time ? date('H:i', strtotime($data->test_report_time)) : '') }}" required>
                    
                        <small class="text-muted">{{__('messages.format_hrs')}}</small>
                        @error('test_report_time') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.test_report_time_required') }}</div>
                    </div>
                </div>
            </div>
         </div>

         <!-- Additional Information -->
         <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.additional_information') }}</h4>
        </div>
         <div class="card">
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-12">
                        <label class="form-label">{{ __('messages.test_instruction') }}</label>
                        <textarea id="instructions" name="instructions" class="form-control" rows="3">{{ old('instructions', $data->instructions) }}</textarea>
                        @error('instructions') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.test_instruction_required') }}</div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">{{ __('messages.test_guidelines_pdf') }}</label>
                    
                        <input type="file" name="guidelines_pdf" class="form-control" accept=".pdf, image/*">
                        @if($data->getGuidelinesPdfAttribute())
                            <div class="mb-2">
                                <a href="{{ $data->getGuidelinesPdfAttribute() }}" target="_blank">{{__('messages.view_pdf')}}</a>
                            </div>
                        @endif
                        @error('guidelines_pdf') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.test_guidelines_pdf_required') }}</div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">{{ __('messages.additional_notes') }}</label>
                        <textarea id="additional_notes" name="additional_notes" class="form-control" rows="3">{{ old('additional_notes', $data->additional_notes) }}</textarea>
                        @error('additional_notes') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.additional_notes_required') }}</div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">{{ __('messages.test_restriction') }}</label>
                        <textarea id="restrictions" name="restrictions" class="form-control" rows="3">{{ old('restrictions', $data->restrictions) }}</textarea>
                        @error('restrictions') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.test_restriction_required') }}</div>
                    </div>
                </div>
            </div>
         </div>

         <!-- Settings -->
        <div class="card-input-title mb-3">
            <h4 class="m-0">{{ __('messages.settings') }}</h4>
        </div>
         <div class="card">
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <label class="form-label d-block">{{ __('messages.lbl_status') }}</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" 
                                class="form-check-input" 
                                name="status" 
                                value="1" 
                                id="statusSwitch" 
                                {{ old('status', $data->status) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusSwitch">
                                <span class="switch-label">{{ __('messages.active') }}</span>
                            </label>
                        </div>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.status_required') }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">{{ __('messages.home_collection') }}</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" 
                                class="form-check-input" 
                                name="is_home_collection_available" 
                                value="1" 
                                id="homeCollectionSwitch" 
                                {{ old('is_home_collection_available', $data->is_home_collection_available) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="homeCollectionSwitch">
                                <span class="switch-label">{{ __('messages.available') }}</span>
                            </label>
                        </div>
                        @error('is_home_collection_available') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.home_collection_required') }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">{{ __('messages.featured') }}</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" 
                                class="form-check-input" 
                                name="is_featured" 
                                value="1" 
                                id="featuredSwitch" 
                                {{ old('is_featured', $data->is_featured) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="featuredSwitch">
                                <span class="switch-label">{{ __('messages.featured') }}</span>
                            </label>
                        </div>
                        @error('is_featured') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.featured_required') }}</div>
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
<script>
$(document).ready(function() {
    // Logo Image Handling
    const $uploadButton = $('#uploadButton');
    const $removeButton = $('#removeButton');
    const $testImageInput = $('#testImageInput');
    const $imagePreview = $('#imagePreview');

    // Replace addEventListener with jQuery on()
    $uploadButton.on('click', function() {
        $testImageInput.trigger('click');
    });

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

    $removeButton.on('click', function() {
        $testImageInput.val('');
        $imagePreview.attr('src', '{{ asset("images/default-logo.png") }}');
        $(this).hide();
    });
    // Initialize Select2
   // Initialize Select2 with tags and AJAX
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

    // Handle equipment creation
    $('#equipment').on('select2:select', function(e) {
        if (e.params.data.newOption) {
            const newValue = e.params.data.text;
            
            // Make AJAX call to store new equipment
            $.ajax({
                url: "{{ route('backend.constants.store') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    name: newValue,
                    type: 'equipment',
                    value: newValue,
                    sequence: 0,
                    status: 1
                },
                success: function(response) {
                    if (response.success) {
                        const $option = $(`#equipment option[value="new:${newValue}"]`);
                        $option.val(response.data.id);
                        toastr.success("{{ __('messages.equipment_added_successfully') }}");
                    } else {
                        toastr.error("{{ __('messages.error') }}");
                        $(`#equipment option[value="new:${newValue}"]`).remove();
                    }
                },
                error: function() {
                    toastr.error("{{ __('messages.error') }}");
                    $(`#equipment option[value="new:${newValue}"]`).remove();
                }
            });
        }
    });

    // Handle test type creation
    $('#test_type').on('select2:select', function(e) {
        if (e.params.data.newOption) {
            const newValue = e.params.data.text;
            
            $.ajax({
                url: "{{ route('backend.constants.store') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    name: newValue,
                    type: 'test_type',
                    value: newValue,
                    sequence: 0,
                    status: 1
                },
                success: function(response) {
                    if (response.success) {
                        const $option = $(`#test_type option[value="new:${newValue}"]`);
                        $option.val(response.data.id);
                        toastr.success("{{__('messages.test_type_added_successfully')}}");
                    } else {
                        toastr.error("{{ __('messages.error') }}");
                        $(`#test_type option[value="new:${newValue}"]`).remove();
                    }
                },
                error: function() {
                    toastr.error("{{ __('messages.error') }}");
                    $(`#test_type option[value="new:${newValue}"]`).remove();
                }
            });
        }
    });

    // Handle lab loading based on vendor selection
    let loggedInVendorId = "{{ auth()->user()->user_type == 'vendor' ? auth()->id() : null }}";
    function loadLab(vendor_id = null) {
        $.ajax({
                url: "{{ route('backend.labs.index_list') }}",
                type: "GET",
                data: { vendor_id: vendor_id },
                success: function(data) {
                    $('#lab_id').empty();
                    $('#lab_id').append('<option value="">{{__("messages.select_lab")}}</option>');
                    $.each(data, function(key, value) {
                        $('#lab_id').append('<option value="' + value.id + '" ' +
                    (selectedLabId == value.id ? 'selected' : '') +
                    '>' + value.name + '</option>');
                    });
                }
            });
        }

        let selectedLabId = "{{ old('lab_id', $data->lab_id) }}";
        if(loggedInVendorId){
        loadLab(loggedInVendorId);
        }else{
            loadLab()
        }
        $('#vendor_id').on('change', function() {
            let vendorId = $(this).val();
      
           if (vendorId) {
             
                loadLab(vendorId);
            } else {
               
                loadLab();
            } 
           
        });


    $('.form-check-input').each(function() {
        updateSwitchLabel($(this));
    });

    $('.form-check-input').on('change', function() {
        updateSwitchLabel($(this));
    });

    function updateSwitchLabel($switch) {
        const $label = $switch.siblings('.form-check-label').find('.switch-label');
        if ($switch.attr('id') === 'statusSwitch') {
            $label.text($switch.is(':checked') ? "{{ __('messages.active') }}" : "{{ __('messages.inactive') }}");
        } else if ($switch.attr('id') === 'homeCollectionSwitch') {
            $label.text($switch.is(':checked') ? "{{ __('messages.available') }}" : "{{ __('messages.not_available') }}");
        } else if ($switch.attr('id') === 'featuredSwitch') {
            $label.text($switch.is(':checked') ? "{{ __('messages.featured') }}" : "{{ __('messages.not_featured') }}");
        }
    }
});
</script>
@endpush
