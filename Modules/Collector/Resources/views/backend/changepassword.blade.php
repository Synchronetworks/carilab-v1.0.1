@extends('backend.layouts.app')
@section('content')
<div class="form-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div class="card-input-title">
            <h4 class="m-0">{{ __('messages.change_password') }}</h4>
        </div>
        <a href="{{ route('backend.collectors.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div> 

   {{ html()->form('POST' ,route('backend.collectors.update_password', $id))
    ->attribute('enctype', 'multipart/form-data')
    ->attribute('data-toggle', 'validator')
    ->attribute('id', 'form-submit') 
    ->class('requires-validation') 
    ->attribute('novalidate', 'novalidate')  
    ->open() }}
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6 col-lg-4">
                    <label for="old_password" class="form-label">{{ __('messages.lbl_old_password') }}<span class="text-danger">*</span></label>
                    <input type="password" class="form-control" value="{{ old('old_password', $data->old_password ?? '') }}"
                        name="old_password" id="old_password" placeholder="{{__('messages.enter_old_password')}}" required>
                        <div class="invalid-feedback" id="name-error">{{ __('messages.old_password_required') }}</div>
                    @error('old_password')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label for="password" class="form-label">{{ __('messages.lbl_new_password') }}<span class="text-danger">*</span></label>
                    <input type="password" class="form-control" value="{{ old('password', $data->password ?? '') }}"
                        name="password" id="password" placeholder="{{__('messages.enter_new_password')}}" required>
                        <div class="invalid-feedback" id="name-error">{{ __('messages.new_password_required') }}</div>
                    @error('password')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label for="password_confirmation" class="form-label">{{ __('messages.lbl_confirm_password') }}<span
                            class="text-danger">*</span></label>
                    <input type="password" class="form-control"
                        value="{{ old('password_confirmation', $data->password_confirmation ?? '') }}"
                        name="password_confirmation" id="password_confirmation" placeholder="{{__('messages.enter_confirm_password')}}" required>
                        <div class="invalid-feedback" id="name-error">{{ __('messages.confirm_password_required') }}</div>
                        @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary')->id('submit-button') }}
    </div>

    {{ html()->form()->close() }}
</div>
@endsection
