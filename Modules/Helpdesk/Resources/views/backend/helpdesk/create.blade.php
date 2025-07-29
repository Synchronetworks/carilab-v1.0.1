@extends('backend.layouts.app')
@section('title'){{__($module_title)}}@endsection 

@section('content')
<div class="form-content">
    {{ html()->form('POST', route('backend.helpdesks.store'))
        ->attribute('enctype', 'multipart/form-data')
        ->class('requires-validation')
        ->attribute('novalidate', 'novalidate')
        ->attribute('id', 'form-submit') 
        ->open()
    }}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
            </div>
            <a href="{{ route('backend.helpdesks.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <div class="card">
            <div class="card-body">                
                {{ html()->hidden('id', $helpdesk->id ?? null) }}
                <div class="row gy-4">
                    <div class="form-group col-md-4">                            
                        {{ html()->label(__('messages.subject') . ' <span class="text-danger">*</span>', 'subject')->class('form-label') }}
                        {{ html()->text('subject', $helpdesk->subject)->placeholder(__('messages.enter_subject'))->class('form-control')  ->required()->attributes(['title' => __('messages.subject_validation')])}}
                        
                        <small class="help-block with-errors text-danger"></small>
                        @error('subject')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="invalid-feedback">{{ __('messages.subject_required') }}</div>

                    </div>
                    @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                        <div class="col-md-4">
                            {{ html()->label(__('messages.select_user') .'<span class="text-danger">*</span>', 'user_id')->class('form-label') }}
                            {{ html()->select('user_id', $users->pluck('full_name', 'id')->prepend(__('messages.select_user'), '')->toArray())
                                ->class('form-select select2')->required() }}
                            @error('user_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback">{{ __('messages.user_required') }}</div>
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.mode') . ' <span class="text-danger">*</span>', 'mode')->class('form-label') }}
                            {{ html()->select('mode',['email' => __('messages.email'),'phone' => __('messages.phone'),'other' => __('messages.other')], $helpdesk->mode)->class('form-select select2')->required()->placeholder(__('messages.select_mode'))->id('mode')}}
                            @error('mode')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback">{{ __('messages.mode_required') }}</div>
                        </div>
                     
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.email'). ' <span class="text-danger asterisk_email d-none">*</span>', 'email')->class('form-label') }}
                            {{ html()->text('email', null)
                                ->attributes(['pattern' => '[^@]+@[^@]+\.[a-zA-Z]{2,}'])
                                ->placeholder(__('messages.placeholder_email'))
                                ->class('form-control')
                                ->id('email')}}
                                 <div class="invalid-feedback">{{ __('messages.email_required') }}</div>
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.contact_number') . ' <span class="text-danger asterisk_phone d-none">*</span>', 'phone_number')->class('form-label') }}
                            {{ html()->input('tel', 'phone_number')
                             ->attribute('value', old('phone_number')) 
                                ->placeholder(__('messages.placeholder_contact_number'))
                                ->class('form-control')
                                ->id('phone_number')}}
                           <div class="invalid-feedback"  id="phone_number-error">{{ __('messages.phone_required') }}</div>
                        </div>
                    @endif
                    <div class="form-group col-md-4">
                        <label class="form-label" for="helpdesk_attachment">{{ __('messages.image') }}</label>
                        <div class="custom-file">
                            <input type="file" onchange="preview()" name="helpdesk_attachment[]" class="custom-file-input form-control"
                                data-file-error="{{ __('messages.files_not_allowed') }}" accept="image/*" multiple>
                        </div>
                    
                        @if ($errors->has('helpdesk_attachment'))
                            <span class="text-danger">{{ $errors->first('helpdesk_attachment') }}</span>
                        @endif
                    
                        @foreach ($errors->get('helpdesk_attachment.*') as $messages)
                            @foreach ($messages as $message)
                                <span class="text-danger">{{ $message }}</span><br>
                            @endforeach
                        @endforeach
                    </div>                    
                </div>
                <div class="row">
                    <div class="form-group col-md-12 mt-4">
                    {{ html()->label(trans('messages.description'). ' <span class="text-danger">*</span>', 'description')->class('form-label') }}
                    {{ html()->textarea('description', $helpdesk->description)->class('form-control textarea')->rows(3)->required()->placeholder(__('messages.enter_description')) }}
                    @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror  
                <div class="invalid-feedback">{{ __('messages.description_required') }}
                </div>
                </div>                            
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            {{ html()->submit( __('messages.save'))->class('btn btn-md btn-primary float-end')->id('submit-button')}}
        </div>
    {{ html()->form()->close() }}
</div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function () {
        $('.custom-file-input').on('change', function() {
        let fileName = '';
        if(this.files && this.files.length > 1) {
            fileName = `${this.files.length} files selected`;
        } else {
            fileName = $(this).val().split('\\').pop();
        }
        if(fileName) {
            $(this).next('.custom-file-label').html(fileName);
        } else {
            $(this).next('.custom-file-label').html('{{ __("messages.no_file_chosen") }}');
        }
    });
        let modeField = $("#mode");
        let emailField = $("#email");
        let contactField = $("#phone_number");

        function validateFields() {
            let selectedMode = modeField.val(); // Get selected value from Select2

            // Hide all asterisks first
            $(".asterisk_email, .asterisk_phone").addClass("d-none");

            // Reset required attributes and clear input values
            emailField.removeAttr("required").val("");
            contactField.removeAttr("required").val("");

            if (selectedMode === "email") {
                $(".asterisk_email").removeClass("d-none"); // Show * for email
                emailField.attr("required", "true");
            } else if (selectedMode === "phone") {
                $(".asterisk_phone").removeClass("d-none"); // Show * for phone
                contactField.attr("required", "true");
            }
        }

        // Listen for Select2 change event
        modeField.on("change.select2", validateFields);

        // Run on page load in case a value is preselected
        validateFields();
    });
</script>
@endpush

