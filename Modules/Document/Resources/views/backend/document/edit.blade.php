@extends('backend.layouts.app')
@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
        <a href="{{ route('backend.documents.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div>
<div class="form-content">
    {{ html()->form('PUT', route('backend.documents.update', $documentdata->id))
        ->attribute('data-toggle', 'validator')
        ->id('documents')
        ->open()  }}
        @csrf
        <div class="card">
            <div class="card-body">                
                {{ html()->hidden('id', $documentdata->id) }}
                <div class="row gy-4">
                    <div class="form-group col-md-4">
                        {{ html()->label(trans('messages.name') . ' <span class="text-danger">*</span>', 'name')->class('form-label') }}
                        {{ html()->text('name', old('name', $documentdata->name))
                            ->placeholder(trans('messages.placeholder_name'))
                            ->class('form-control')
                            ->required() 
                        }}
                        <small class="help-block with-errors text-danger"></small>
                    </div>

                    <div class="col-md-4">
                        <label for="user_type" class="form-label">{{ __('messages.lbl_user_type') }}<span class="text-danger">*</span></label>
                        <select class="form-select select2" name="user_type" id="user_type" required>
                            <option value="" disabled selected>{{ __('messages.select_user_type') }}</option>
                            <option value="vendor" {{ old('user_type', $documentdata->user_type) == 'vendor' ? 'selected' : '' }}>
                                {{ __('messages.lbl_vendor') }}
                            </option>
                            <option value="collector" {{ old('user_type', $documentdata->user_type) == 'collector' ? 'selected' : '' }}>
                                {{ __('messages.lbl_collector') }}
                            </option>
                        </select>
                        @error('user_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="user_type-error">{{__('messages.user_type_required')}}</div>
                    </div>

                    <div class="form-group col-md-4">
                        {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                        <div class="d-flex align-items-center justify-content-between form-control">
                            {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                            <div class="form-check form-switch">
                                {{ html()->hidden('status', 0) }}
                                {{ html()->checkbox('status', old('status', $documentdata->status))
                                    ->class('form-check-input')
                                    ->id('status')
                                }}
                            </div>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>     
                    </div>                   
            
                    <div class="form-group col-md-12">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            {{ html()->checkbox('is_required', old('is_required', $documentdata->is_required))
                                ->class('custom-control-input')
                                ->id('is_required')
                            }}                                    
                            <label class="custom-control-label" for="is_required">{{ __('messages.is_required') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary') }}
        </div>
    {{ html()->form()->close() }}
</div>
@endsection
