

@extends('setting::backend.setting.index')

@section('settings-content')
{{ html()->form('POST', route('backend.setting.store'))
    ->attribute('data-toggle', 'validator')
    ->attribute('id', 'form-submit')  // Add the id attribute here
    ->class('requires-validation')  // Add the requires-validation class
    ->attribute('novalidate', 'novalidate')  // Disable default browser validation
    ->attribute('enctype', 'multipart/form-data')
    ->open()
}}
    @csrf
    <div>
        <h4 class="mb-4"><i class="ph ph-piggy-bank"></i> {{ __('messages.commision_settings') }} </h4>
    </div>
        @if(multivendor()==1)
            <!-- Vendor Commission Card -->

            <div class="mb-4">
                <div class="form-group pb-3 mb-3 border-bottom">
                    <h4 class="mb-3">{{ __('messages.vendor_commission') }}</h4>
                    <div class="form-group">
                        <div class="d-flex gap-3 flex-wrap align-items-center">
                            <div class="form-check">
                                {{ html()->radio('vendor_commission_type',true,'global')
                                    ->attribute('id', 'vendor_global', 'class', 'demo')
                                    ->attribute('class', 'form-check-input')
                                    ->attribute('onchange', 'toggleVendorCommission("global")')
                                    ->checked(empty($settings['vendor_commission_type']) || $settings['vendor_commission_type'] == 'global')
            
                                }}
                                {{ html()->label(__('messages.global'), 'vendor_global') }}
                            </div>
        
                            <div class="form-check">
                                {{ html()->radio('vendor_commission_type', false, 'per_vendor')
                                    ->attribute('id', 'vendor_per_vendor')
                                    ->attribute('class', 'form-check-input')
                                    ->attribute('onchange', 'toggleVendorCommission("per_vendor")')
                                    ->checked(isset($settings['vendor_commission_type']) && $settings['vendor_commission_type'] == 'per_vendor')
            
                                }}
                                {{ html()->label(__('messages.per_vendor'), 'vendor_per_vendor') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif

            <!-- Collector Commission Card -->
            <div>
                <!-- Commission Type Radio Buttons -->
                <div class="form-group">
                    <h4 class="mb-3">{{ __('messages.collector_commission') }}</h4>
                    <div class="d-flex gap-3 flex-wrap align-items-center">
                        <div class="form-check">
                            {{ html()->radio('collector_commission_type', true, 'global')
                                ->attribute('id', 'collector_global')
                                ->attribute('class', 'form-check-input')
                                ->attribute('onchange', 'toggleCollectorCommission("global")')
                                ->checked(empty($settings['collector_commission_type']) || $settings['collector_commission_type'] == 'global')
                            }}
                            {{ html()->label(__('messages.global'), 'collector_global') }}
                        </div>

                        <div class="form-check">
                            {{ html()->radio('collector_commission_type', false, 'per_collector')
                                ->attribute('id', 'collector_per_collector')
                                ->attribute('class', 'form-check-input')
                                ->attribute('onchange', 'toggleCollectorCommission("per_collector")')
                                ->checked(isset($settings['collector_commission_type']) && $settings['collector_commission_type'] == 'per_collector')
                            }}
                            {{ html()->label(__('messages.per_collector'), 'collector_per_collector') }}
                        </div>
                    </div>
                </div>
            </div>

        <div class="form-group mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary float-right" id="submit-button">
                {{ __('messages.save') }}
            </button>
        </div>
    </form>
    @endsection
    @push('after-scripts')
    <script>
        // Toggle Vendor Commission Fields
        function toggleVendorCommission(type) {
            document.getElementById('vendor_global_fields').style.display = type === 'global' ? 'block' : 'none';
            document.getElementById('vendor_per_vendor_fields').style.display = type === 'per_vendor' ? 'block' : 'none';
        }
    
        // Toggle Collector Commission Fields
        function toggleCollectorCommission(type) {
            document.getElementById('collector_global_fields').style.display = type === 'global' ? 'block' : 'none';
            document.getElementById('collector_per_collector_fields').style.display = type === 'per_collector' ? 'block' : 'none';
        }
    </script>