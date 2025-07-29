@extends('setting::backend.setting.index')

@section('settings-content')
<form method="POST" action="{{ route('backend.setting.store') }}">
    @csrf

    <div class="card">
        <div class="card-header p-0 mb-4">
            <h4><i class="ph ph-faders-horizontal"></i> {{ __('messages.lbl_misc_setting') }} </h4>
        </div>

        <div class="card-body p-0">
            <div class="row row-cols-1 row-cols-md-2 gy-4">
                <div class="col">
                    {{ html()->label(__('messages.lbl_google_analytics'))->class('form-label') }}
                    {{ html()->text('google_analytics')
                        ->class('form-control')
                        ->placeholder(__('messages.lbl_google_analytics'))
                        ->value(old('google_analytics', $settings['google_analytics'] ?? '')) }}
                    @error('google_analytics')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col">
                    {{ html()->label(__('messages.lbl_language'))->class('form-label') }}
                    {{ html()->select('default_language')
                        ->options(array_column($languages, 'name', 'id'))
                        ->class('form-select select2')
                        ->value(old('default_language', $settings['default_language'] ?? '')) }}
                    @error('default_language')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col">
                    {{ html()->label(__('messages.lbl_timezone'))->class('form-label') }}
                    {{ html()->select('default_time_zone')
                        ->options(array_column($timezones, 'text', 'id'))
                        ->class('form-select select2')
                        ->value(old('default_time_zone', $settings['default_time_zone'] ?? '')) }}
                    @error('default_time_zone')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
          

            <div class="form-group col">
                <label for="date_format" class="col-sm-12 form-label">{{ __('messages.date_format') }}</label>
                <div class="col-sm-12">
                    <select class="form-select select2 select2js date_format" name="date_format" id="date_format">
                        @foreach(dateFormatList() as $formatCode => $format)
                            <option value="{{ $formatCode }}" {{ isset($settings['date_format']) && $settings['date_format'] == $formatCode ? 'selected' : '' }}>{{ $format }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group col">
                <label for="time_format" class="col-sm-12 form-label">{{ __('messages.time_formate') }}</label>
                <div class="col-sm-12">
                    <select class="form-select select2 time_format" name="time_format" id="time_format">
                        @foreach(timeFormatList() as $timeFormat)
                            <option value="{{ $timeFormat['format'] }}" {{ isset($settings['time_format']) && $settings['time_format'] == $timeFormat['format'] ? 'selected' : '' }}>{{ $timeFormat['time'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
           <div class="form-group col">
            <label for="default_currency" class="col-sm-12 form-label">{{ __('messages.currency') }}</label>
            <div class="col-sm-12">
                <select class="form-select select2 currency" name="default_currency" id="default_currency">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" 
                            {{ isset($settings['default_currency']) && $settings['default_currency'] == $currency->id ? 'selected' : '' }}>
                            {{ $currency->text }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

            <div class="form-group col">
                <label for="" class="col-sm-12 form-label">{{ __('messages.currency_position') }}</label>
                <div class="col-sm-12">
                    {{ html()->select('currency_position', ['left' => __('messages.left'), 'right' => __('messages.right')], $settings['currency_position'] ?? 'left')->class('form-select select2 select2js') }}            
                </div>
            </div>

            @php
                
                $settings = (array) $settings;
            @endphp

            <div class="form-group col">
                {{ html()->label(trans('messages.google_map_keys'), 'google_map_keys')->class('col-sm-12 form-label') }}
                <div class="col-sm-12">
                    {{ html()->text('google_map_keys', $settings['google_map_keys'] ?? '')->id('google_map_keys')->class('form-control')->placeholder(trans('messages.google_map_keys')) }}
                </div>
                <small class="help-block with-errors text-danger"></small>
            </div>

            <div class="form-group col">
                <label for="" class="col-sm-12 form-label">{{ __('messages.latitude') }}</label>
                <div class="col-sm-12">
                    {{ html()->text('latitude', $settings['latitude'] ?? '')->class('form-control')->placeholder(trans('messages.latitude'))->id('latitude') }}
                </div>
            </div>

            <div class="form-group col">
                <label for="" class="col-sm-12 form-label">{{ __('messages.longitude') }}</label>
                <div class="col-sm-12">
                    {{ html()->text('longitude', $settings['longitude'] ?? '')->class('form-control')->placeholder(trans('messages.longitude'))->id('longitude') }}
                </div>
            </div>

            <div class="form-group col">
                <label for="" class="col-sm-12 form-label">{{ __('messages.distance_type') }}</label>
                <div class="col-sm-12">
                    {{ html()->select('distance_type', ['km' => __('messages.km'), 'mile' => __('messages.mile')], $settings['distance_type'] ?? 'km')->class('form-select select2 select2js') }}
                </div>
            </div>

            <div class="form-group col">
                <label for="" class="col-sm-12 form-label">{{ __('messages.radious') }}</label>
                <div class="col-sm-12">
                    {{ html()->number('radious', $settings['radious'] ?? 50)->class('form-control')->placeholder('50')->id('radious') }}
                </div>
            </div>

            <div class="form-group col">
                <label for="" class="col-sm-12 form-label">{{ __('messages.digitafter_decimal_point') }}</label>
                <div class="col-sm-12">
                    {{ html()->number('digitafter_decimal_point', $settings['digitafter_decimal_point'] ?? 1)->class('form-control')->placeholder('1')->id('digitafter_decimal_point') }}
                </div>
            </div>

            <div class="form-group col">
                <label for="" class="col-sm-12 form-label">{{ __('messages.copyright_text') }}</label>
                <div class="col-sm-12">
                    {{ html()->text('settings_copyright', $settings['settings_copyright'] ?? '')->class('form-control')->placeholder(__('messages.copyright_text')) }}
                </div>
            </div>

            <div class="form-group col">
                <label for="data_table_limit" class="col-sm-12 form-label">{{ __('messages.data_table_limit') }}</label>
                <div class="col-sm-12">
                    @php
                        $lengthOptions = [
                            5 => '5',
                            10 => '10',
                            15 => '15',
                            20 => '20',
                            25 => '25',
                            100 => '100',
                            -1 => 'All'
                        ];
                    @endphp
                    {{ html()->select('data_table_limit')
                        ->class('form-select select2')
                        ->options($lengthOptions)
                        ->value(old('data_table_limit', $settings['data_table_limit'] ?? 10))
                        ->id('data_table_limit') }}
                    <small class="text-muted">{{ __('messages.records_per_page') }}</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group  d-flex justify-content-between">
                    <label for="android_app_links" class="form-label">{{ __('messages.android_app_links') }}</label>
                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="hidden" name="android_app_links" value="0">
                        <input type="checkbox" class="custom-control-input toggle-section" name="android_app_links" id="android_app_links" value="1" {{ old('android_app_links', $settings['android_app_links'] ?? 0) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="android_app_links"></label>
                    </div>
                </div>
                
                <div class="form-padding-box mb-3 {{ old('android_app_links', $settings['android_app_links'] ?? 0) ? '' : 'd-none' }}" id="android_app">
                    <div class="row gy-4">
                        <div class="form-group col-sm-12">
                            {{ html()->label(trans('messages.customer_playstore_url'), 'playstore_url')->class('form-label') }}
                            {{ html()->text('playstore_url', $settings['playstore_url'] ?? '')->class('form-control')->placeholder(trans('messages.customer_playstore_url'))->id('playstore_url') }}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ html()->label(trans('messages.collector_play_store_url'), 'provider_playstore_url')->class('form-label') }}
                            {{ html()->text('provider_playstore_url', $settings['provider_playstore_url'] ?? '')->class('form-control')->placeholder(trans('messages.collector_play_store_url'))->id('provider_playstore_url') }}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group d-flex justify-content-between">
                    <label for="ios_app_links" class="form-label">{{ __('messages.ios_app_links') }}</label>
                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="hidden" name="ios_app_links" value="0">
                        <input type="checkbox" class="custom-control-input toggle-section" name="ios_app_links" id="ios_app_links" value="1" {{ old('ios_app_links', $settings['ios_app_links'] ?? 0) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="ios_app_links"></label>
                    </div>
                </div>
                
                <div class="form-padding-box mb-3 {{ old('ios_app_links', $settings['ios_app_links'] ?? 0) ? '' : 'd-none' }}" id="ios_app">
                    <div class="row gy-4">
                        <div class="form-group col-sm-12">
                            {{ html()->label(trans('messages.customer_appstore_url'), 'appstore_url')->class('form-label') }}
                            {{ html()->text('appstore_url', $settings['appstore_url'] ?? '')->class('form-control')->placeholder(trans('messages.customer_appstore_url'))->id('appstore_url') }}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ html()->label(trans('messages.collector_app_store_url'), 'provider_appstore_url')->class('form-label') }}
                            {{ html()->text('provider_appstore_url', $settings['provider_appstore_url'] ?? '')->class('form-control')->placeholder(trans('messages.collector_app_store_url'))->id('provider_appstore_url') }}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
            <div class="text-end mt-3">
                {{ html()->button(__('messages.save'))
                    ->type('submit')
                    ->class('btn btn-primary') }}
            </div>
        </div>
    </div>
</form>
@endsection

@push('after-scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {
    const toggleSections = document.querySelectorAll('.toggle-section');

    toggleSections.forEach(toggle => {
        toggle.addEventListener('change', function () {
            const targetSection = document.getElementById(this.id.replace('_links', ''));
            if (this.checked) {
                targetSection.classList.remove('d-none');
            } else {
                targetSection.classList.add('d-none');
            }
        });
    });
});

</script>
@endpush