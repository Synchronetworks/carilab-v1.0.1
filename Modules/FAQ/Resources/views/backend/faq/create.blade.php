@extends('backend.layouts.app')
@section('title') {{ __($module_title) }} @endsection


@section('content')
<div class="form-content">
    {{ html()->form('POST', route('backend.faqs.store'))
            ->attribute('enctype', 'multipart/form-data')
            ->attribute('data-toggle', 'validator')
            ->attribute('id', 'form-submit')  // Add the id attribute here
            ->class('requires-validation')  // Add the requires-validation class
            ->attribute('novalidate', 'novalidate')  // Disable default browser validation
            ->open() }}
    @csrf
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div class="card-input-title">
            <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
        </div>
        <a href="{{ route('backend.faqs.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row gy-4">
                <!-- Question Field -->
                <div class="col-md-6">
                    {{ html()->label(__('messages.lbl_question') . ' <span class="text-danger">*</span>', 'question')->class('form-label') }}
                    {{ html()->text('question')
                            ->attribute('value', old('question'))
                            ->placeholder(__('messages.enter_question'))
                            ->class('form-control')
                            ->required() }}
                    @error('question')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="invalid-feedback" id="question-error">{{__('messages.question_required')}}</div>
                </div>

                <!-- Status Field -->
                <div class="col-md-6">
                    {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                    <div class="d-flex align-items-center justify-content-between form-control">
                        {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }}
                        <div class="form-check form-switch">
                            {{ html()->hidden('status', 1) }}
                            {{ html()->checkbox('status', old('status', true))
                                    ->class('form-check-input')
                                    ->id('status') }}
                        </div>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <!-- Answer Field -->
                <div class="col-md-12">
                    {{ html()->label(__('messages.lbl_answer') . ' <span class="text-danger">*</span>', 'answer')->class('form-label') }}
                    {{ html()->textarea('answer')
                            ->attribute('value', old('answer'))
                            ->placeholder(__('messages.enter_answer'))
                            ->class('form-control')
                            ->required() }}
                    @error('answer')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="invalid-feedback" id="answer-error">{{__('messages.answer_required')}}</div>
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
