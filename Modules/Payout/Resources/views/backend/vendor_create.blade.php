@extends('backend.layouts.app')
@section('title') 
    {{ __($module_title) }} 
@endsection
@section('content')
    <div class="container-fluid">
        <div>
            <x-back-button-component route="backend.payouts.index" />
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('backend.payouts.vendor_store') }}" method="POST" enctype="multipart/form-data" class="requires-validation" id="form-submit" novalidate>
                            @csrf
                            {{ html()->hidden('user_id',$vendor->id ?? null) }}
                            {{ html()->hidden('user_type',$vendor->user_type ?? null) }}
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        {{ __('messages.method') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="payment_method" id="method" class="form-select select2" required>
                                        @if($vendor->banks->count() > 0)
                                            <option value="bank">{{ __('messages.bank') }}</option>
                                        @endif
                                        <option value="cash" selected>{{ __('messages.cash') }}</option>
                                        <option value="wallet">{{ __('messages.wallet') }}</option>
                                    </select>
                                </div>

                                <!-- Bank Selection (Only for "Bank" payment method) -->
                                <div class="col-md-4 d-none" id="select_bank">
                                    <label class="form-label">
                                        {{ __('messages.select_bank') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="bank" id="bank" class="form-select select2">
                                        <option value="">{{ __('messages.select_bank') }}</option>
                                        @foreach($vendor->banks as $bank)
                                            <option value="{{ $bank->id }}" 
                                                    data-bank-name="{{ $bank->bank_name }}"
                                                    data-branch-name="{{ $bank->branch_name }}"
                                                    data-account-no="{{ $bank->account_no }}"
                                                    data-phone="{{ $bank->phone_number }}">
                                                {{ $bank->bank_name }} - {{ substr($bank->account_no, -4) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Amount Field -->
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('messages.amount') }}</label>
                                    <input type="text" class="form-control" value="{{ Currency::format($vendorearning ?? 0) }}" readonly>
                                    <input type="hidden" name="amount" value="{{ old('amount', $vendorearning) }}">
                                </div>

                                <!-- Payment Gateway (Only for "Bank" Payment Method) -->
                                <div class="form-group col-md-12 d-none" id="payment_gateway">
                                    <label class="form-label">
                                        {{ __('messages.payment_gateway') }}
                                    </label>
                                    <div class="d-flex align-items-center flex-sm-nowrap flex-wrap gap-3">
                                        <label class="form-check form-control px-5 cursor-pointer">
                                            <input class="form-check-input" type="radio" name="payment_gateway" value="razorpayx">
                                            <span class="form-check-label">{{__('messages.lbl_razorpayx')}}</span>
                                        </label>
                                        <label class="form-check form-control px-5 cursor-pointer">
                                            <input class="form-check-input" type="radio" name="payment_gateway" value="stripe">
                                            <span class="form-check-label">{{__('messages.lbl.stripe')}}</span>
                                        </label>
                                    </div>
                                    <small class="help-block with-errors text-danger"></small>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">{{__('messages.lbl_description')}} </label>
                                    <textarea class="form-control" name="description" id="description" rows="3">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary float-end">
                                        {{ __('messages.save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const paymentMethodSelect = document.getElementById('method');
    const selectBankDiv = document.getElementById('select_bank');
    const paymentGatewayDiv = document.getElementById('payment_gateway');
    const bankSelect = document.getElementById('bank');
    const gatewayRadios = document.querySelectorAll('input[name="payment_gateway"]');

    function toggleFields() {
        const selectedMethod = paymentMethodSelect.value;

        if (selectedMethod === 'bank') {
            selectBankDiv.classList.remove('d-none'); 
            paymentGatewayDiv.classList.remove('d-none'); 
            bankSelect.setAttribute('required', true);
            gatewayRadios.forEach(radio => radio.setAttribute('required', true));
        } else {
            selectBankDiv.classList.add('d-none'); 
            paymentGatewayDiv.classList.add('d-none'); 
            bankSelect.removeAttribute('required');
            gatewayRadios.forEach(radio => radio.removeAttribute('required'));
        }
    }

    // Run on page load
    setTimeout(toggleFields, 500); 

    // Use jQuery if available (for better compatibility)
    if (window.jQuery) {
        $(document).on('change', '#method', function() {
            toggleFields();
        });
    } else {
        // Add vanilla JavaScript event listener
        paymentMethodSelect.addEventListener('change', function() {
            toggleFields();
        });
    }


   
   
});

</script>
@endpush


@endsection