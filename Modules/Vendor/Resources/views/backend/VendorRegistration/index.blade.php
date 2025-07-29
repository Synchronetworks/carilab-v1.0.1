<x-auth-layout>
    <x-slot name="title">
        @lang('Register')
    </x-slot>

        <x-slot name="logo">
            <a href="#">
                <x-application-logo class="w-20 h-20 " />
            </a>
        </x-slot>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10 col-sm-12">
                    <div class="text-center mb-5 pb-4">
                        <a href="{{ url('login') }}">
                            <img src="{{ asset(setting('logo')) }}" class="img-fluid w-25">
                        </a> 
                    </div>
                    <!-- Tab Navigation for Steps -->
                    <div class="d-flex justify-content-center mb-5">
                        <div class="bg-primary-subtle rounded-pill p-3">
                            <ul class="nav nav-pills vendar-reg-tab" id="step-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="step-1-tab" data-bs-toggle="pill" href="#step-1" role="tab" aria-controls="step-1" aria-selected="true"> 
                                        <span class="no">01</span>
                                        <i class="ph-fill ph-check-circle"></i>
                                    </a>  
                                    <div class="setup-data">
                                        <small class="text-primary fw-bold">{{ __('messages.step_1') }}</small>
                                        <h6>{{ __('messages.vendor_basic_detail') }}</h6>
                                    </div>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="step-2-tab" data-bs-toggle="pill" href="#step-2" role="tab" aria-controls="step-2" aria-selected="false">
                                        <span class="no">02</span>
                                        <i class="ph-fill ph-check-circle"></i>
                                    </a>
                                    <div class="setup-data">
                                        <small class="text-primary fw-bold">{{ __('messages.step_2') }}</small>
                                        <h6>{{ __('messages.upload_document') }}</h6>
                                    </div>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="step-3-tab" data-bs-toggle="pill" href="#step-3" role="tab" aria-controls="step-3" aria-selected="false">
                                        <span class="no">03</span>
                                        <i class="ph-fill ph-check-circle"></i>
                                    </a>
                                    <div class="setup-data">
                                        <small class="text-primary fw-bold">{{ __('messages.step_3') }}</small>
                                        <h6>{{ __('messages.choose_plan') }}</h6>
                                    </div>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="step-4-tab" data-bs-toggle="pill" href="#step-4" role="tab" aria-controls="step-4" aria-selected="false">
                                        <span class="no">04</span>
                                        <i class="ph-fill ph-check-circle"></i>
                                    </a>
                                    <div class="setup-data">
                                        <small class="text-primary fw-bold">{{ __('messages.step_4') }}</small>
                                        <h6>{{ __('messages.lab_basic_detail') }}</h6>
                                    </div>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="step-5-tab" data-bs-toggle="pill" href="#step-5" role="tab" aria-controls="step-5" aria-selected="false">
                                        <span class="no">05</span>
                                        <i class="ph-fill ph-check-circle"></i>
                                    </a>
                                    <div class="setup-data">
                                        <small class="text-primary fw-bold">{{ __('messages.step_5') }}</small>
                                        <h6>{{ __('messages.select_test_case') }}</h6>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div id="step-content">
                                <!-- Dynamic step content will go here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

     

</x-auth-layout>

<script>
    $(document).ready(function() {
    let currentStep = "{{$step}}";
    let vendorId = null;
    let labId=null
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
let appUrl = "{{ url('') }}";  
    function loadStep(step) {
        $.ajax({
            url: appUrl +'/vendor/wizard/step/' + step, 
            method: 'GET',
            data: {
                vendor_id: vendorId,  
                lab_id:labId
            },
            success: function(response) {
                
                $('#step-content').html(response);
                updateStepTabs(step);
            },
            error: function(xhr, status, error) {
                alert('There was an error loading the step.');
            }
        });
    }
    function showLoader(button) {
        button.prop('disabled', true);
        button.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('messages.loading') }}`);
    }

    function hideLoader(button, originalText) {
        button.prop('disabled', false);
        button.html(originalText);
    }
 

   
    $(document).on('click', '.next-btn', function() {
        
        event.preventDefault(); 
        let button = $(this);
        let originalText = button.html();

       
        showLoader(button);
            
            if (!validateRequiredInputs()) {
                hideLoader(button, originalText);
                return;
            }
     
        let formData = new FormData($('#step-form')[0]);

       if ( vendorId) {
            formData.append('vendor_id', vendorId);  // Include vendor ID if it's missing
        }
        $.ajax({
            url: appUrl +'/vendor/wizard/step/' + currentStep + '/store',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                
                if (response.success) {
                   
                    if(response.vendor_id){
                        vendorId=response.vendor_id;
                    }
                    if(response.lab_id){
                        labId=response.lab_id
                    }
                    currentStep++;
                    loadStep(currentStep);
                }else {
                    hideLoader(button, originalText);
                }
            },
            error: function(xhr) {
                hideLoader(button, originalText);
                if (xhr.status === 422) {
                   
                    $('.text-danger').remove();
                    
                    
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        let inputField = $('[name="' + key + '"]');
                        inputField.after('<span class="text-danger">' + value[0] + '</span>');
                    });
                }
            },
        });

       
    });
    
        
        function validateRequiredInputs() {
            let isValid = true;

            $('input[required], select[required], textarea[required],input[data-required="1"]').each(function() {
                let input = $(this);
                let errorMessageDiv = $('#' + input.attr('id') + '-error');
                
                if (input.is('input[type="file"]')) {
                   
                    if (input[0].files.length === 0) {
                       
                        errorMessageDiv.text('This field is required.').show();
                        input.addClass('is-invalid');
                        input.removeClass('is-valid');
                        isValid = false;
                    } else {
                       
                        errorMessageDiv.hide();
                        input.removeClass('is-invalid');
                        input.addClass('is-valid');
                    }
                } else if (input.is('input[type="text"], input[type="email"], input[type="password"], textarea')) {
                   
                    if (input.val().trim() === '') {
                        
                        errorMessageDiv.text('This field is required.').show();
                        input.addClass('is-invalid');
                        input.removeClass('is-valid');
                        isValid = false;
                    } else {
                        
                        errorMessageDiv.hide();
                        input.removeClass('is-invalid');
                        input.addClass('is-valid');
                    }
                } else if (input.is('select')) {
                    
                    if (input.val() === null || input.val() === '') {
                        
                        errorMessageDiv.text('This field is required.').show();
                        input.addClass('is-invalid');
                        input.removeClass('is-valid');
                        isValid = false;
                    } else {
                        
                        errorMessageDiv.hide();
                        input.removeClass('is-invalid');
                        input.addClass('is-valid');
                    }
                } else if (input.is('input[type="time"]')) {
                   
                    if (input.val().trim() === '') {
                       
                        errorMessageDiv.text('This field is required.').show();
                        input.addClass('is-invalid');
                        input.removeClass('is-valid');
                        isValid = false;
                    } else {
                        
                        errorMessageDiv.hide();
                        input.removeClass('is-invalid');
                        input.addClass('is-valid');
                    }
                } else if (input.is('input[type="number"]')) {
                   
                    if (input.val() === '' || isNaN(input.val())) {
                       
                        errorMessageDiv.show();
                        input.addClass('is-invalid');
                        input.removeClass('is-valid');
                        isValid = false;
                    } else {
                        
                        errorMessageDiv.hide();
                        input.removeClass('is-invalid');
                        input.addClass('is-valid');
                    }
                } else if (input.is('input[type="date"]')) {
                   
                    if (input.val().trim() === '') {
                        
                        errorMessageDiv.show();
                        input.addClass('is-invalid');
                        input.removeClass('is-valid');
                        isValid = false;
                    } else {
                        
                        errorMessageDiv.hide();
                        input.removeClass('is-invalid');
                        input.addClass('is-valid');
                    }
                }else if (input.is('input[type="tel"]')) {
                   
                    if (input.val().trim() === '') {
                        
                        errorMessageDiv.show();
                        input.addClass('is-invalid');
                        input.removeClass('is-valid');
                        isValid = false;
                    } else {
                        
                        errorMessageDiv.hide();
                        input.removeClass('is-invalid');
                        input.addClass('is-valid');
                    }
                }
            });

            return isValid;
        }



       
       function updateStepTabs(step) {
       
        for (let i = 1; i <= 5; i++) {
            let tab = $(`#step-${i}-tab`);
       
            if (i < step) {
                tab.removeClass('disabled');
                tab.css('pointer-events', 'auto');
                tab.removeClass('active').addClass('bg-success text-white');
            } else if (i == step) {
                tab.removeClass('disabled'); 
                tab.css('pointer-events', 'auto');
                tab.addClass('active').removeClass('bg-success').removeClass('text-white').addClass('bg-primary text-white');
            } else {
                tab.addClass('disabled'); 
                tab.css('pointer-events', 'none'); 
                tab.removeClass('active').removeClass('bg-success').removeClass('bg-primary').removeClass('text-white');
            }
        }
    }
   
    loadStep(currentStep);
    updateStepTabs(currentStep)
    
         
});





    </script>
