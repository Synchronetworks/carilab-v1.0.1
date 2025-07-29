@extends('backend.layouts.app')
@section('title', isset($collector_document->id) ? __('messages.edit_collector_document') :
    __('messages.new_collector_document'))
@section('content')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="">{{ __('messages.basic_information') }}</h4>
            </div>
            <a href="{{ route('backend.collectordocument.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('backend.collectordocument.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('collector_document')->open() }}
                        {{ html()->hidden('id', $collector_document->id ?? null) }}
                        <div class="row gy-4">
                            @if (auth()->user()->hasAnyRole(['admin', 'demo_admin', 'vendor']))
                                <div class="form-group col-md-4">
                                    {{ html()->label(
                                            __('messages.select_name', ['select' => __('messages.collector')]) . ' <span class="text-danger">*</span>',
                                            'collector_id',
                                        )->class('form-label') }}
                                    <br />

                                    @php

                                        $collectorOptions =
                                            isset($collector) && count($collector) > 0
                                                ? $collector->pluck('full_name', 'id')->toArray()
                                                : [];
                                        $selectedCollector =
                                            isset($collectordata) && !empty($collectordata->id)
                                                ? $collectordata->id
                                                : null;

                                    @endphp

                                    {{ html()->select(
                                            'collector_id',
                                            ['' => __('messages.select_name', ['select' => __('messages.collectors')])] + $collectorOptions,
                                            old('collector_id', $selectedCollector),
                                        )->class('select2 form-group collectors ' . ($errors->has('collector_id') ? 'is-invalid' : ''))->required()->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.collector')])) }}
                                    @error('collector_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback">{{ __('messages.collector_required') }}</div>
                                </div>
                            @endif

                            @php
                                $is_required = optional($collector_document->document)->is_required == 1 ? '*' : '';
                            @endphp

                            <div class="form-group col-md-4">
                                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-1">
                                    <span>
                                        {{ html()->label(
                                                __('messages.select_document', ['select' => __('messages.document')]) . ' <span class="text-danger">*</span>',
                                                'document_id',
                                            )->class('form-label me-5 mb-0') }}
                                    </span>
                                    <a href="{{ route('backend.documents.create') }}">
                                        <i class="fa fa-plus-circle mt-2"></i>
                                        {{ trans('messages.add_form_title', ['form' => trans('messages.document')]) }}
                                    </a>
                                </div>
                                {{ html()->select(
                                        'document_id',
                                        ['' => __('messages.select_name', ['select' => __('messages.document')])] +
                                            $documents->pluck('name', 'id')->toArray(),
                                        old('document_id', optional($collector_document->document)->id),
                                    )->class('select2 form-group document_id')->id('document_id')->required()->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.document')])) }}
                                @error('document_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>

                            @if (auth()->user()->hasAnyRole(['admin', 'demo_admin', 'vendor']))
                                <div class="col-md-6 col-lg-4">
                                    <label for="is_verified" class="form-label">{{ __('messages.is_verify') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center justify-content-between form-control">
                                        <label for="is_verified"
                                            class="form-label mb-0 text-body">{{ __('messages.verified') }}</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_verified" value="0">
                                            <!-- Hidden input field -->
                                            <input class="form-check-input" type="checkbox" id="is_verified"
                                                name="is_verified" value="1"
                                                {{ old('is_verified', $collector_document->is_verified) == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @error('is_verified')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label for="status" class="form-label"> {{ __('messages.lbl_status') }}</label>
                                    <div class="d-flex align-items-center justify-content-between form-control">
                                        <label for="status" class="form-label mb-0 text-body">
                                            {{ __('messages.active') }}</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="status" value="0"> <!-- Hidden input field -->
                                            <input class="form-check-input" type="checkbox" id="status" name="status"
                                                value="1"
                                                {{ old('status', $collector_document->status) == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <div class="form-group col-md-4">
                                {{ html()->label(
                                        __('messages.upload_document') . ' <span class="text-danger asterisk d-none">*</span>',
                                        'collector_document',
                                    )->class('form-label') }}
                                <div class="custom-file">

                                    @if (isset($collector_document) && $collector_document->hasMedia('collector_document'))
                                        <input type="file" id="collector_document" name="collector_document"
                                            class="custom-file-input form-control"
                                            {{ $collector_document || getMediaFileExit($collector_document, 'collector_document') ? '' : 'required' }}>
                                        <div class="mb-3">
                                            <label>{{ __('messages.current_document') }}:</label>
                                            <a href="{{ $collector_document->getFirstMediaUrl('collector_document') }}"
                                                target="_blank">{{ __('messages.view_document') }}</a>
                                        </div>
                                    @else
                                        <input type="file" id="collector_document" name="collector_document"
                                            class="custom-file-input form-control collectordocument">
                                    @endif
                                </div>
                            </div>

                        </div>

                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('after-scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            function updateDocumentRequired() {
                let documentId = $('#document_id').val();
                if (documentId) {
                    $.ajax({
                        url: "{{ route('backend.documents.check-required') }}",
                        type: 'GET',
                        data: {
                            document_id: documentId
                        },
                        success: function(response) {
                            if (response.is_required) {
                                $('.asterisk').removeClass('d-none');
                                $('.collectordocument').prop('required', true);

                            } else {
                                $('.asterisk').addClass('d-none');
                                $('.collectordocument').prop('required', false);
                            }
                        }
                    });
                }
            }

            updateDocumentRequired();
            $('#document_id').on('change', updateDocumentRequired);
        });
    </script>
@endpush
