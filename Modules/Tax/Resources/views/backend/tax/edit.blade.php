@extends('backend.layouts.app')
@section('title') {{ __($module_title) }} @endsection


@section('content')
<div class="form-content">
    {{ html()->form('POST' ,route('backend.taxes.update', $data->id))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')
        ->attribute('id', 'form-submit')  // Add the id attribute here
        ->class('requires-validation')  // Add the requires-validation class
        ->attribute('novalidate', 'novalidate')  // Disable default browser validation
        ->open() }}

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="m-0">{{__('messages.basic_information')}}</h4>
            </div>
            <a href="{{ route('backend.taxes.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                @csrf
                @method('PUT')
                <div class="row gy-4">
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_name') . ' <span class="text-danger">*</span>', 'title')->class('form-label') }}
                        {{ html()->text('title')
                            ->attribute('value', $data->title)
                            ->placeholder(__('messages.placeholder_name'))
                            ->class('form-control')
                            ->required() }}
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.title_required')}}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_Type') . ' <span class="text-danger">*</span>', 'type')->class('form-label') }}
                        {{ html()->select('type', ['Fixed' => __('messages.fixed'), 'Percentage' => __('messages.percentage')])
                                ->class('form-select select2')
                                ->value($data->type) 
                                ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.lbl_Type')]))
                                ->required() }}
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.type_required')}}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        {{ html()->label(__('messages.lbl_value')  .'<span class=" type-symbol"> (%) </span>'.' <span class="text-danger">*</span>', 'value')->class('form-label') }}
                        {{ html()->number('value')
                         ->attribute('min', '0')
                                ->attribute('step', 'any')
                                ->attribute('oninput', "validity.valid||(value='')")
                                ->attribute('value',$data->value)
                                ->placeholder(__('messages.placeholder_value'))
                                ->class('form-control')
                                ->required() }}
                        @error('value')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.value_required')}}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
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
        <div class="d-flex justify-content-end">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-right')->id('submit-button') }}
        </div>
    {{ html()->form()->close() }}
</div>
@endsection
@push('after-scripts')
<script type="text/javascript">
    $(document).ready(function() {
        function updateValueSymbol() {
            const type = $('#type').val();
            const symbol = type === 'Percentage' ? ' (%) ' :  '({{ getCurrencySymbol() }})';
            $('.type-symbol').text(symbol);
        }
        // Initial setup
        updateValueSymbol();

        // Update on type change
        $('#type').on('change', function() {
            updateValueSymbol();
        });
    });
</script>
@endpush