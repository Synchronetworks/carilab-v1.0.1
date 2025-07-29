@extends('backend.layouts.app')
@section('title') {{__($module_title)}} @endsection

@section('content')
<div class="form-content">
{{ html()->form('POST' ,route('backend.prescriptions.update', $data->id))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
    @csrf
    <div class="card">
        <div class="card-body">        
            @method('PUT')
            <div class="row gy-4">
                <div class="col-sm-12">
                    {{ html()->label(__('plan.lbl_name') . ' <span class="text-danger">*</span>', 'name')->class('form-label') }}
                        {{ html()->text('name')
                                ->attribute('value', $data->name)  ->placeholder(__('messages.lbl_plan_name'))
                                ->class('form-control')
                        }}
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-end">
        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary') }}
    </div>
    {{ html()->form()->close() }}
</div>
@endsection
