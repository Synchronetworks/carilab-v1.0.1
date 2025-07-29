@extends('backend.layouts.app')
@section('title') {{__($module_action)}} {{__($module_title)}} @endsection


@section('content')
    <div class="form-content">
        <form action="{{ route('backend.packagemanagements.update', $data->id ?? null) }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
            @csrf
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="card-input-title">
                    <h4 class="m-0">{{__('messages.basic_information')}}</h4>
                </div>
                <a href="{{ route('backend.packagemanagements.index') }}" class="btn btn-sm btn-primary">
                    <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
                </a>
            </div>
            <!-- Basic Information -->
            <div class="card">
                <div class="card-body">                    
                    @method('PUT')
                    {{ html()->hidden('id',$data->id ?? null) }}                        
                    <div class="row gy-4">                            
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{__('messages.package_image')}}</label>
                                <div class="btn-file-upload">
                                    <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                        <img id="imagePreview" 
                                            src="{{ $data->getPackageImageAttribute() ?? asset('images/default-avatar.png') }}" 
                                            alt="package Image" class="img-thumbnail avatar-150 object-cover">
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
                                <input type="file" name="package_image" id="packageimageInput" class="form-control d-none" accept="image/*">
                                @error('package_image') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row gy-4">
                                <div class="col-md-12">
                                    <label for="name" class="form-label">{{ __('messages.package_name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" 
                                        value="{{ $data->name ?? old('name') }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{__('messages.package_name_required')}}</div>
                                </div>
                                <div class="col-md-12">
                                    <label for="description" class="form-label">{{__('messages.lbl_description')}}</label>
                                    <textarea class="form-control" name="description" id="description" rows="3">{{ $data->description ?? old('description') }}</textarea>
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{__('messages.description_required')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor & Tax Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{  __('messages.test_case_information')  }}</h4>
            </div>
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
                                        <option value="{{ $vendor->id }}" {{ $data->vendor_id == $vendor->id ? 'selected' : '' }}>
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
                            <label class="form-label">{{__('messages.lbl_lab')}}<span class="text-danger">*</span></label>
                            <select class="form-select select2" name="lab_id" id="lab_id" required>
                                <option value="">{{__('messages.select_lab')}}</option>
                                @foreach($labs as $lab)
                                    <option value="{{ $lab->id }}" {{ $data->lab_id == $lab->id ? 'selected' : '' }}>
                                        {{ $lab->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lab_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.lab_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="catalog_id" class="form-label">{{__('messages.test_case')}}<span class="text-danger">*</span></label>
                            <select name="catalog_id[]" id="catalog_id" class="form-select select2" multiple required>
                                <option value="">{{__('messages.select_case')}}</option>
                                @foreach($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}" 
                                        {{ in_array($catalog->id, old('catalog_id', $data->catalog_id)) ? 'selected' : '' }}>
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                           
                            @error('catalog_id') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.case_required')}}</div>
                        </div>                        
                    </div>
                </div>
            </div>
            <!-- Package Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.package_information')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">                        
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <label class="form-label">{{__('messages.lbl_price')}}({{getCurrencySymbol()}})<span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" min="0" step="any" value="{{ $data->price ?? old('price') }}" required>
                            @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.price_required')}}</div>
                        </div>                            
                        <div class="col-md-4">
                            <label for="start_at" class="form-label">{{__('messages.start_date')}}<span class="text-danger">*</span></label>
                            <input type="date" class="form-control datetimepicker" name="start_at" id="start_at" 
                                    value="{{ old('start_at', $data->start_at ? date('Y-m-d', strtotime($data->start_at)) : '') }}" required>
                            @error('start_at')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback" id="start_at-error">{{__('messages.start_date_applied')}}</div>
                        </div>                        
                        <div class="col-md-4">
                            <label for="end_at" class="form-label">{{__('messages.end_date')}}<span class="text-danger">*</span></label>
                            <input type="date" class="form-control datetimepicker" name="end_at" id="end_at" 
                                    value="{{ old('end_at', $data->end_at ? date('Y-m-d', strtotime($data->end_at)) : '') }}" required>
                            @error('end_at')
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                            <div class="invalid-feedback">{{__('messages.end_date_required')}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Information -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.discount_information')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <label for="discount" class="form-label">{{__('messages.lbl_discount')}}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="discount" class="form-label mb-0 text-body">{{__('messages.active')}}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="discount" name="is_discount" {{ $data->is_discount == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            @error('is_discount') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.discount_required')}}</div>
                        </div>
                        <div class="col-md-4 d-none" id="discount_type">
                            <label for="discount_type" class="form-label">{{__('messages.lbl_discount_type')}}</label>
                            <select name="discount_type" id="discount_type" class="form-select select2">
                                <option value="" selected>Select Discount Type</option>
                                <option value="percentage" {{ $data->discount_type == 'percentage' ? 'selected' : '' }}>{{ __('messages.percentage') }}</option>
                                <option value="fixed" {{ $data->discount_type == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed') }}</option>
                            </select>
                            @error('discount_type') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.discount_type_required')}}</div>
                        </div>
                        <div class="col-md-4 d-none" id="discount_price">
                            <label for="discount_price" class="form-label">{{__('messages.lbl_discount_price')}}</label>
                            <input type="number" class="form-control" name="discount_price" min="0" step="any" id="discount_price" 
                                value="{{ $data->discount_price ?? old('discount_price') }}">
                            @error('discount_price') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.discount_price_required')}}</div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Status -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.lbl_status')}}</h4>
            </div>
            <div class="card">
                <div class="card-body">                           
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <label for="status" class="form-label">{{__('messages.lbl_status')}}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="status" class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" 
                                        value="1" {{ $data->status == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{__('messages.status_required')}}</div>
                        </div>
                        <div class="col-md-4">
                            <label for="is_featured" class="form-label">{{__('messages.featured')}}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="is_featured" class="form-label mb-0 text-body">{{__('messages.active')}}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                        value="1" {{ $data->is_featured == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="is_home_collection_available" class="form-label">{{ __('messages.home_collection') }}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="is_home_collection_available" class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_home_collection_available" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_home_collection_available" name="is_home_collection_available" 
                                        value="1" {{ $data->is_home_collection_available == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end">
                <button type="submit" class="btn btn-primary">
                    {{__('messages.save')}}
                </button>
            </div>
        </form>
    </div>
@endsection


@push('after-scripts')
<script>
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
    $(document).ready(function() {
       
 
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
        // packageimage Image Handling
        const $uploadButton = $('#uploadButton');
const $removeButton = $('#removeButton');
const $packageImageInput = $('#packageimageInput');
const $imagePreview = $('#imagePreview');

$uploadButton.on('click', function() {
    $packageImageInput.trigger('click');
});

$packageImageInput.on('change', function() {
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
    $packageImageInput.val('');
    $imagePreview.attr('src', '{{ asset("images/default-logo.png") }}');
    $(this).hide();
});




        // Handle lab loading based on vendor selection
        $('#vendor_id').on('change', function() {
            let vendorId = $(this).val();
            if(vendorId) {
                $.ajax({
                    url: "{{ route('backend.labs.index_list') }}",
                    type: "GET",
                    data: { vendor_id: vendorId },
                    success: function(data) {
                        $('#lab_id').empty();
                        $('#lab_id').append('<option value="">Select Lab</option>');
                        $.each(data, function(key, value) {
                            $('#lab_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#lab_id').empty();
                $('#lab_id').append('<option value="">Select Lab</option>');
            }
        });

        $('#lab_id').on('change', function() {
            let labId = $(this).val();
            let vendorId = $('#vendor_id').val();
            if(labId) {
                $.ajax({
                    url: "{{ route('backend.catlogmanagements.index_list') }}",
                    type: "GET",
                    data: { lab_id: labId, vendor_id: vendorId },
                    success: function(data) {
                        $('#catalog_id').empty();
                        $('#catalog_id').append('<option value="">Select Catalog</option>');
                        $.each(data, function(key, value) {
                            
                            $('#catalog_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }

                });
            } else {
                $('#catalog_id').empty();
                $('#catalog_id').append('<option value="">Select Catalog</option>');
            }
        });

        var discount = $("input[name='is_discount']").prop('checked');
        checkDiscount(discount);

        $('#discount').change(function() {
            value = $(this).prop('checked') == true ? true : false;
            checkDiscount(value);
        });

        function checkDiscount(value) {
            if (value == true) {
                $('#discount_type').removeClass('d-none');
                $('#discount_price').removeClass('d-none');
                $('#discount_type').prop('required', true);
                $('#discount_price').prop('required', true);
            } else {
                $('#discount_type').addClass('d-none');
                $('#discount_price').addClass('d-none');
                $('#discount_type').val('');
                $('#discount_price').val('');
                $('#discount_type').removeAttr('required');
                $('#discount_price').removeAttr('required');
            }
        }


     
    });
</script>
@endpush