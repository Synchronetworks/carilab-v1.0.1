@extends('setting::backend.setting.index')

@section('settings-content')
<form action="{{ route('backend.labs.importLab')}}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
@csrf    
<h3 class="mb-4"> <i class="ph ph-tree-structure"></i> {{ __('messages.import_data_setting') }}</h3>
    <div class="row row-cols-1 row-cols-md-2 gy-4">
        <div class="col">
            <label for="lab" class="form-label">{{ __('messages.from_lab') }}<span class="text-danger">*</span></label>
            <select class="form-select select2" name="lab_id" id="lab_id" required>   
                <option value="" disabled selected>{{ __('messages.select_lab') }}</option>               
                @foreach ($labs as $lab)
                <option value="{{ $lab->id }}"{{ old('lab_id') == $lab->id ? 'selected' : '' }}>
                    {{ $lab->name }}
                </option>
                @endforeach  
            </select>
            <div class="invalid-feedback">{{ __('messages.lab_field_required') }}</div>
        </div>
        <div class="col">
            <label for="lab" class="form-label">{{ __('messages.to_lab') }}<span class="text-danger">*</span></label>
            <select class="form-select select2" name="import_lab_id[]" id="import_lab_id" multiple required data-placeholder="{{ __('messages.select_lab') }}">
            </select>
            <div class="invalid-feedback">{{ __('messages.lab_field_required') }}</div>
        </div>

        <div class="col">
            <label for="test_case" class="form-label">{{ __('messages.lbl_test_case') }}<span class="text-danger">*</span></label>
            <select class="form-select select2" name="test_case[]" id="test_case" multiple required style="width:100%" data-placeholder="{{ __('messages.select_test_case') }}">
            </select>
            <div class="invalid-feedback">{{ __('messages.test_case_field_required') }}</div>
        </div>

        <div class="col">
            <label for="test_package" class="form-label">{{ __('messages.test_package') }}</label>
            <select class="form-select select2" name="test_package[]" id="test_package" multiple style="width:100%" data-placeholder="{{ __('messages.select_test_package') }}">
            </select>
            <div class="invalid-feedback">{{ __('messages.package_field_required') }}</div>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-primary">
            {{__('messages.save')}}
        </button>
    </div>
</form>
@endsection


@push('after-scripts')

<script>
   $(document).ready(function() {


    
    $('#lab_id').on('change', function() {
        var lab_id = $(this).val();
       
        $("#test_case").html('<option value="">{{ __("messages.select_case") }}</option>'); // Reset test_case dropdown
       $("#test_package").html('<option value="">{{ __("messages.select_package") }}</option>'); // Reset test_package dropdown
        if (!lab_id) return; // Stop execution if lab_id is empty

        

        // Fetch test cases
        $.ajax({
            url: "{{ route('backend.catlogmanagements.index_list') }}",
            type: "GET",
            data: { lab_id: lab_id },
            dataType: 'json',
            success: function(response) {
               

                if (Array.isArray(response) && response.length > 0) {
                    $.each(response, function(index, value) {
                        if (value.id && value.name) {
                            $('#test_case').append(`<option value="${value.id}">${value.name} 
                                ${value.price ? ' - ' + currencyFormat(value.price) : ''}</option>`);
                        }
                    });
                }
                
                $('#test_case').select2(); // Ensure select2 is applied
                $('#test_case').trigger('change'); // Refresh select2 dropdown
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        // Fetch test packages
        $.ajax({
            url: "{{ route('backend.packagemanagements.index_list') }}",
            type: "GET",
            data: { lab_id: lab_id },
            dataType: 'json',
            success: function(response) {
                

                if (Array.isArray(response) && response.length > 0) {
                    $.each(response, function(index, value) {
                        if (value.id && value.name) {
                            $('#test_package').append(`<option value="${value.id}">${value.name} 
                                ${value.price ? ' - ' + currencyFormat(value.price) : ''}</option>`);
                        }
                    });
                } else {
                
                }
                
                $('#test_package').select2(); // Ensure select2 is applied
                $('#test_package').trigger('change'); // Refresh select2 dropdown
            },
            error: function(xhr) {
                console.error( xhr.responseText);
            }
        });

         // Fetch test packages
         $.ajax({
            url: "{{ route('backend.labs.index_list') }}",
            type: "GET",
            data: { lab_id: lab_id },
            dataType: 'json',
            success: function(response) {
               

                if (Array.isArray(response) && response.length > 0) {
                    $.each(response, function(index, value) {
                        if (value.id && value.name) {
                            $('#import_lab_id').append(`<option value="${value.id}">${value.name} 
                                ${value.price ? ' - ' + currencyFormat(value.price) : ''}</option>`);
                        }
                    });
                } else {
                  
                }
                
                $('#import_lab_id').select2(); // Ensure select2 is applied
                $('#import_lab_id').trigger('change'); // Refresh select2 dropdown
            },
            error: function(xhr) {
                console.error( xhr.responseText);
            }
        });
    });

    // Initialize select2 on load
    $('#test_case').select2();
    $('#test_package').select2();
});

        
</script>
@endpush