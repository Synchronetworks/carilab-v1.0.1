@extends('backend.layouts.app')
@section('title',isset($vendor_document->id) ? __('messages.edit_vendor_document') : __('messages.new_vendor_document'))
@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
        <a href="{{ route('backend.vendordocument.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{ html()->form('POST', route('backend.vendordocument.store'))->attribute('enctype', 'multipart/form-data')->class('requires-validation')->attribute('data-toggle', 'validator')->attribute('novalidate', 'novalidate') ->id('vendor_document_form')->open() }}
                            {{ html()->hidden('id',$vendor_document->id ?? null) }}
                            <div class="row gy-4">
                                @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.select_name', ['select' => __('messages.lbl_vendor')]) . ' <span class="text-danger">*</span>', 'vendor_id')
                                    ->class('form-label') 
                                }}
                                @if(!empty($vendordata))
                                    {{ html()->select('vendor_id', ['' => __('messages.select_name', ['select' => __('messages.lbl_vendor')])] + [$vendordata->id => $vendordata->full_name], $vendordata->id)
                                        ->class('select2 form-group vendors')
                                        ->required()
                                    }}
                                @else
                                    {{ html()->select('vendor_id', ['' => __('messages.select_name', ['select' => __('messages.lbl_vendor')])] + $vendor->pluck('full_name', 'id')->toArray(), null)
                                        ->class('select2 form-group vendors')
                                        ->required()
                                    }}
                                @endif
                                @error('vendor_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback">{{ __('messages.select_name', ['select' => __('messages.lbl_vendor')]) }}</div>

                                </div>
                                @endif
                    
                                @php
                                    $is_required = optional($vendor_document->document)->is_required == 1 ? '*' : '';
                                @endphp
                    
                    <div class="form-group col-md-4">
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-1">
                            <span> 
                            {{ html()->label(__('messages.select_document', ['select' => __('messages.document')]) . ' <span class="text-danger">*</span>', 'document_id')
                                ->class('form-label mb-0')
                            }}
                            </span>
                            <a href="{{ route('backend.documents.create') }}">
                                <i class="fa fa-plus-circle mt-2"></i> {{ trans('messages.add_form_title', ['form' => trans('messages.document')]) }}
                            </a>
                        </div>
                        {{ html()->select('document_id', 
                        ['' => __('messages.select_name', ['select' => __('messages.document')])] + $documents->pluck('name', 'id')->toArray(), 
                        optional($vendor_document->document)->id)
                        ->class('select2 form-group document_id')
                        ->id('document_id')
                        ->required()
                    }}    
                    
                    @error('document_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="invalid-feedback">{{ __('messages.select_name', ['select' => __('messages.document')]) }}</div>

                    </div>
                    
                                @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                                <div class="col-md-6 col-lg-4">
                                    <label for="is_verified" class="form-label">{{ __('messages.is_verify') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center justify-content-between form-control">
                                        <label for="is_verified" class="form-label mb-0 text-body">{{ __('messages.verified') }}</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_verified" value="0"> <!-- Hidden input field -->
                                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified"
                                                value="1" {{ old('is_verified', $data->is_verified ?? 1) == 1 ? 'checked' : '' }} required>
                                        </div>
                                    </div>
                                    @error('is_verified')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label for="status" class="form-label"> {{ __('messages.lbl_status') }}</label>
                                    <div class="d-flex align-items-center justify-content-between form-control">
                                        <label for="status" class="form-label mb-0 text-body"> {{ __('messages.active') }}</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="status" value="0"> <!-- Hidden input field -->
                                            <input class="form-check-input" type="checkbox" id="status" name="status"
                                                value="1" {{ old('status', $data->status ?? 1) == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @endif
                    
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.upload_document') . ' <span class="text-danger document-required-asterisk"></span>', 'vendor_document')
                                        ->class('form-label')
                                    }}
                                    <div class="custom-file">
                                        <input type="file" id="vendor_document" name="vendor_document" class="custom-file-input form-control" 
                                            @if(!$vendor_document || !getMediaFileExit($vendor_document, 'vendor_document')) 
                                                
                                            @endif>
                                        @if($vendor_document && getMediaFileExit($vendor_document, 'vendor_document'))
                                            <label class="custom-file-label upload-label">{{ $vendor_document->getFirstMedia('vendor_document')->file_name }}</label>
                                        @endif
                                        <div class="invalid-feedback">{{ __('messages.document_required') }}</div>

                                    </div>
                                </div>
                    
                                @if(getMediaFileExit($vendor_document, 'vendor_document'))
                                <div class="col-md-2 mb-2 position-relative">
                                    @php
                                        $file_extensions = config('constant.IMAGE_EXTENTIONS');
                                        $image = getSingleMedia($vendor_document,'vendor_document');
                                        $ext = strtolower(imageExtention($image));
                                        $isImage = in_array($ext, $file_extensions);
                                        $isPdf = $ext === 'pdf';
                                    @endphp
                            
                                    @if($isImage)   
                                        <!-- Image Preview -->
                                        <img id="vendor_document_preview" src="{{ $image }}" alt="#" class="attachment-file img-thumbnail avatar-150 object-cover">
                                    @elseif($isPdf)
                                        <!-- PDF Preview -->
                                        <embed src="{{ $image }}" type="application/pdf" width="150px" height="150px" class="attachment-file img-thumbnail avatar-150 object-cover">
                                    @else
                                        <!-- Default Icon for Other Files -->
                                        <img id="vendor_document_preview" src="{{ asset('images/file.png') }}" class="attachment-file img-thumbnail avatar-150 object-cover">
                                    @endif
                            
                                    <!-- Download Link -->
                                    <a href="{{ $image }}" class="d-block mt-2" download target="_blank">
                                        <i class="fas fa-download"></i> {{ __('messages.download') }}
                                    </a>
                                </div>
                            @endif
                            </div>
                    
                        </div>
                    </div>
                    {{ html()->submit(trans('messages.save'))
                        ->class('btn btn-md btn-primary float-end')
                    }}
                {{ html()->form()->close() }}
        </div>
    </div>
</div>
    @endsection
    @push('after-scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            // Function to update document required status
            function updateDocumentRequired() {
                let documentId = $('#document_id').val();
                let fileInput = $('#vendor_document');
                if(documentId) {
                    $.ajax({
                        url: "{{ route('backend.documents.check-required') }}",
                        type: 'GET',
                        data: { document_id: documentId },
                        success: function(response) {
                            if(response.is_required) {
                                $('.document-required-asterisk').text('*');
                                fileInput.prop('required', true);
                            

                            } else {
                                $('.document-required-asterisk').text('');
                                fileInput.prop('required', false);
                            }
                        }
                    });
                }
            }
    
            // Call on document ready and when document selection changes
            updateDocumentRequired();
            $('#document_id').on('change', updateDocumentRequired);

            
        });
    </script>
    @endpush