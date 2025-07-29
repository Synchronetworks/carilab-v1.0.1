@extends('backend.layouts.app')
@section('title'){{__($module_title)}}@endsection


@section('content')
<div class="form-content">
<x-back-button-component :route="'backend.'. $data->user_type .'_commisions.index'" />
    {{ html()->form('POST' ,route('backend.commisions.update', $data->id))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')
        ->attribute('id', 'form-submit')  // Add the id attribute here
        ->class('requires-validation')  // Add the requires-validation class
        ->attribute('novalidate', 'novalidate')  // Disable default browser validation
        ->open() }}
        <div class="card">
            <div class="card-body">
                @csrf
                @method('PUT')
                {{ html()->hidden('user_type', $data->user_type) }}
                {{ html()->hidden('id',$data->id ?? null) }}
                <div class="row">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                        {{ html()->label(__('messages.lbl_name') . ' <span class="text-danger">*</span>', 'title')->class('form-label') }}
                        {{ html()->text('title')
                                ->attribute('value', $data->title)
                                ->placeholder(__('messages.placeholder_name'))
                                ->class('form-control')
                                ->required() }}
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.name_required')}}</div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        {{ html()->label(__('messages.lbl_Type') . ' <span class="text-danger">*</span>', 'type')->class('form-label') }}
                        {{ html()->select('type', ['Fixed' => 'Fixed', 'Percentage' => 'Percentage'])
                                ->class('form-select select2')
                                ->value($data->type) 
                                ->attributes(['oninvalid' => "this.setCustomValidity('" . __('messages.type_required') . "')", 'oninput' => "this.setCustomValidity('')"])
                                ->required() }}
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.type_required')}}</div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        {{ html()->label(__('messages.lbl_value') . ' <span class="text-danger">*</span>', 'value')->class('form-label') }}
                        {{ html()->number('value')
                                ->attribute('value',$data->value)
                                ->attribute('min', '0')
                                ->attribute('step', 'any')
                                ->placeholder(__('messages.placeholder_value'))
                                ->class('form-control')
                                ->required() }}
                        @error('value')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.value_required')}}</div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                            <div class="d-flex align-items-center justify-content-between form-control">
                                {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                                <div class="form-check form-switch">
                                    {{ html()->hidden('status', 0) }}
                                    {{
                                    html()->checkbox('status',$data->status)
                                        ->class('form-check-input')
                                        ->id('status')
                                    }}
                                </div>
                            </div>
                        @error('status')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary')->id('submit-button') }}
        </div>
    {{ html()->form()->close() }}
</div>

@endsection
