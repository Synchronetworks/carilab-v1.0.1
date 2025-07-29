@extends('backend.layouts.app')
@section('title'){{__($module_action)}} {{__($module_title)}}@endsection
@section('content')

{{ html()->form('PUT' ,route('backend.planlimitation.update', $data->id))
        ->attribute('enctype', 'multipart/form-data')
        ->attribute('data-toggle', 'validator')
        ->attribute('id', 'form-submit')  // Add the id attribute here
        ->class('requires-validation')  // Add the requires-validation class
        ->attribute('novalidate', 'novalidate')  // Disable default browser validation
        ->open()
}}

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div class="card-input-title">
        <h4 class="m-0">{{__('messages.plan_information')}}</h4>
    </div>
    <a href="{{ route('backend.planlimitation.index') }}" class="btn btn-sm btn-primary">
        <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row gy-4">
            <div class="col-md-6">
                {{ html()->label(__('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-label') }}
                {{ html()->text('title')
                        ->attribute('value',$data->title)->placeholder(__('messgaes.lbl_plan_limit_title'))
                        ->class('form-control')
                        ->attribute('required','required')
                }}
                @error('title')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="invalid-feedback" id="name-error">{{__('messages.title_required')}}</div>
            </div>

            <div class="col-md-6">
                {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                <div class="d-flex justify-content-between align-items-center form-control">
                    {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                    <div class="form-check form-switch">
                        {{ html()->hidden('status', 0) }}
                        {{
                                html()->checkbox('status',$data->status )
                                    ->class('form-check-input')
                                    ->id('status')
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
                ->placeholder(__('messages.lbl_plan_limit_description'))
                ->class('form-control')
                ->attribute('required','required')
                }}
                @error('description')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="invalid-feedback" id="name-error">{{ __('messages.description_required') }}</div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end">
    {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-right')->id('submit-button') }}
</div>
{{ html()->form()->close() }}

@endsection
