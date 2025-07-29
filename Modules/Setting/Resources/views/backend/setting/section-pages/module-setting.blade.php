@extends('setting::backend.setting.index')

@section('settings-content')


<div class="col-md-12 mb-3 d-flex justify-content-between">
    <h5><i class="fa-solid fa-sliders"></i> {{ __('messages.lbl_module-setting') }}</h5>

</div>

    <form method="POST" action="{{ route('backend.setting.store') }}" id="payment-settings-form">
        @csrf
        <div class="form-group border-bottom pb-2">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label m-0 fw-normal" for="category-is_multi_vendor">{{ __('setting_module_page.lbl_multi_vendor') }} </label>
                <input type="hidden" value="0" name="is_multi_vendor">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input"
                           type="checkbox"
                           name="is_multi_vendor"
                           id="is_multi_vendor"
                           value="1"
                           {{ old('is_multi_vendor', $settings['is_multi_vendor'] ?? 0) == 1 ? 'checked' : '' }} />
                </div>
            </div>
        </div>
        
       

        <div class="text-end">
        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
        </div>
    </form>

@endsection

@push('after-scripts')

<script>


    

</script>
@endpush

