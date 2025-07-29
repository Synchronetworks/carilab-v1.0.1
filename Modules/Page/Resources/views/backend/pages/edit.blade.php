@extends('backend.layouts.app')
@section('title'){{$data->name}}@endsection
@section('content')
<div class="form-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div class="card-input-title">
            <h4 class="m-0">{{__('messages.basic_information')}}</h4>
        </div>
        <a href="{{ route('backend.pages.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div>
    {{ html()->form('POST' ,route('backend.pages.update', $data->id))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')
        ->attribute('id', 'form-submit')  // Add the id attribute here
        ->class('requires-validation')  // Add the requires-validation class
        ->attribute('novalidate', 'novalidate')  // Disable default browser validation
        ->open() }}
        @csrf
        <div class="card">
            <div class="card-body">
                @method('PUT')
                {{-- Name row --}}
                <div class="row gy-3">
                    <div class="col-md-6">
                        {{ html()->label(__('messages.lbl_name') . ' <span class="text-danger">*</span>', 'name')->class('form-label') }}
                        {{ html()->text('name')
                                ->attribute('value', $data->name)
                                ->placeholder(__('messages.placeholder_name'))
                                ->class('form-control')
                                ->attribute('required','required')
                            }}
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.name_required')}} </div>
                    </div>
                    <div class="col-md-6">
                        {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                        <div class="d-flex align-items-center justify-content-between form-control">
                            {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                            <div class="form-check form-switch">
                                {{ html()->hidden('status', 0) }}
                                {{
                                    html()->checkbox('status', $data->status)
                                        ->class('form-check-input')
                                        ->id('status')
                                        ->value(1)
                                }}
                            </div>
                        </div>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        {{ html()->label(__('messages.lbl_description') . ' <span class="text-danger">*</span>', 'description')->class('form-label') }}
                        {{ html()->textarea('description', $data->description)
                                ->placeholder(__('messages.lbl_description'))
                                ->class('form-control')
                                ->attribute('required','required')
                        }}
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.description_required')}} </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary') }}
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

});

$(document).on('click', '.variable_button', function() {
    const textarea = $(document).find('.tab-pane.active');
    const textareaID = textarea.find('textarea').attr('id');
    tinyMCE.activeEditor.selection.setContent($(this).attr('data-value'));
});
</script>
@endpush
