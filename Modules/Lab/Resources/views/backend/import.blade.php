@extends('backend.layouts.app')
@section('title'){{__($module_title)}}@endsection

@section('content')
    <div class="form-content"> 
        <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
            <a href="{{ route('backend.labs.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('backend.labs.importLab')}}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
                    @csrf
                    <input type="hidden" name="import_lab_id" id="import_lab_id" value="{{ $labId }}">
                    
                    <div class="row mb-4">
                        <div class="col-md-4 mb-4">
                        <label for="lab" class="form-label">{{__('messages.lbl_lab')}}<span class="text-danger">*</span></label>
                            <select class="form-select select2" name="lab_id" id="lab_id" required>
                                <option value="" disabled selected> {{ __('messages.select_name', ['select' => __('messages.lbl_lab')]) }}</option>
                                @foreach ($labs as $lab)
                                <option value="{{ $lab->id }}"{{ old('lab_id') == $lab->id ? 'selected' : '' }}>
                                    {{ $lab->name }}
                                </option>
                                @endforeach                  
                            </select>
                            <div class="invalid-feedback">{{__('messages.lab_required')}}</div></div>

                        <div class="col-md-4 mb-4">
                        <label for="test_case" class="form-label">{{__('messages.test_case')}}<span class="text-danger">*</span></label>
                            <select class="form-select select2" name="test_case[]" id="test_case" multiple >
                            
                        </select>
                        <div class="invalid-feedback">{{__('messages.test_case_field_required')}}</div></div>

                        <div class="col-md-4 mb-4">
                        <label for="test_package" class="form-label">{{__('messages.test_package')}}</label>
                            <select class="form-select select2" name="test_package[]"  id="test_package" multiple >
                                    
                            </select>
                            <div class="invalid-feedback">{{__('messages.package_field_required')}}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary float-end">
                                {{__('messages.save')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                
                $('#test_case').select2({
                    placeholder: "{{ __('messages.select_name', ['select' => __('messages.lbl_test_case')]) }}",
                    allowClear: true
                }); // Ensure select2 is applied
                $('#test_case').trigger('change'); // Refresh select2 dropdown
            },
            error: function(xhr) {
                console.error("{{ __('messages.error_fetching_test_cases') }}:", xhr.responseText);
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
                }
                
                $('#test_package').select2({
                    placeholder: "{{ __('messages.select_name', ['select' => __('messages.packages')]) }}",
                    allowClear: true
                });// Ensure select2 is applied
                $('#test_package').trigger('change'); // Refresh select2 dropdown
            },
            error: function(xhr) {
                console.error("{{ __('messages.error_fetching_test_packages') }}:", xhr.responseText);
            }
        });
    });

    // Initialize select2 on load
    $('#test_case').select2({
        placeholder: "{{ __('messages.select_name', ['select' => __('messages.lbl_test_case')]) }}",
        allowClear: true,
     
    });
    
    $('#test_package').select2({
        placeholder: "{{ __('messages.select_name', ['select' => __('messages.packages')]) }}",
        allowClear: true,
   
    });


    $('#form-submit').on('submit', function(e) {
        e.preventDefault();
        
        // Reset previous error states
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');
        
        let isValid = true;
        const labId = $('#lab_id').val();
        const testCases = $('#test_case').val();
        const testPackages = $('#test_package').val();
        
        // Validate Lab selection
        if (!labId) {
            $('#lab_id').addClass('is-invalid');
            $('#lab_id').siblings('.invalid-feedback').show();
            isValid = false;
        }
        
        // Validate that either test cases or test packages are selected
        if ((!testCases || testCases.length === 0) && (!testPackages || testPackages.length === 0)) {
            $('#test_case').addClass('is-invalid');
            $('#test_package').addClass('is-invalid');
            $('#test_case').siblings('.invalid-feedback').text('{{ __("messages.case_required") }}').show();
                       isValid = false;
        }
        
        // If validation passes, submit the form
        if (isValid) {
            if (isValid) {
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            
            // Submit form using .on()
            $(this).on('submit', function(e) {
                e.preventDefault();
                const form = this;
                $(form).trigger('submit');
            }).trigger('submit');
        } else {
            // Scroll to first error
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    
    // Clear validation errors when selection changes
    $('#lab_id, #test_case').on('change', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').hide();
    });

});

        
</script>
@endpush