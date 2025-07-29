
@extends('backend.layouts.app')
@section('content')
<form method="POST" id="form" action="{{ isset($data) ? route('backend.taxes.update', $data->id) : route('backend.taxes.store') }}" enctype="multipart/form-data">
    @csrf
    @if (isset($data->id))
        @method('PUT')
    @endif
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <label for="title" class="form-label">{{__('messages.title')}}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ old('title', $data->title ?? '') }}" name="title" id="title" placeholder="{{__('messages.enter_title')}" required>
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6 mb-3">
                    <label for="type" class="form-label">{{__('messages.type')}}<span class="text-danger">*</span></label>
                    <select class="form-select" name="type" id="type" required>
                        <option value="fixed" {{ old('type', $data->type ?? '') == 'fixed' ? 'selected' : '' }}>{{__('messages.fixed')}}</option>
                        <option value="percentage" {{ old('type', $data->type ?? '') == 'percentage' ? 'selected' : '' }}>{{__('messages.percentage')}}</option>
                    </select>
                    @error('type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6 mb-3">
                    <label for="value" class="form-label">{{__('messages.value')}}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ old('value', $data->value ?? '') }}" name="value" id="value" placeholder="{{__('messages.placeholder_value')}}" required>
                    @error('value')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6 mb-3">
                    <label for="status" class="form-label">{{__('messages.lbl_status')}}</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="status" value="0"> <!-- Hidden input field -->
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $data->status ?? '') == 1 ? 'checked' : '' }}>
                    </div>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{('messages.save_changes')}}</button>
        </div>
    </div>
</form>

@endsection

