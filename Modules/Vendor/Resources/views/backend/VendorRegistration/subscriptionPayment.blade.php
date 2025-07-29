<div class="section-spacing-bottom">
    <div class="mt-5">
        <div class="row gy-4">
            <div class="col-lg-3">
                <form id="plan-form">
                    @foreach ($plans as $plan)
                        <div class="bg-body p-3 rounded mb-4">
                            <label class="form-check stripe-payment-form p-4 position-relative rounded" for="{{ strtolower($plan->name) }}" data-amount="{{ $plan->price }}">
                                <input type="radio" id="{{ strtolower($plan->id) }}" name="plan_name" value="{{ $plan->id }}" data-amount="{{ $plan->price }}" class="form-check-input payment-radio-btn">
                                <span class="form-check-label">
                                    <span class="h4 text-primary mb-2 d-block">{{  Currency::format($plan->price ?? 0) }} <small class="fw-medium text-body">/ {{ $plan->duration_value }} {{ ucfirst($plan->duration) }}</small></span>
                                    @if($plan->discount == 1)
                                    <span class="heading-color fw-medium d-block">{{ $plan->discount_percentage }}  % off</span> 
                                    @endif
                                    <span class="heading-color fw-bold d-block">{{ $plan->name }}</span>                                    
                                </span>
                            </label>
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="col-lg-9">
                <form action="{{ route('process-payment') }}" method="POST" id="payment-form">
                    @csrf
                    <div class="bg-body p-3 rounded mb-4">
                        <div class="form-group">
                            <input type="hidden" id="selected-plan-id" name="plan_id">
                            <input type="hidden" id="selected-price" name="price">
                            <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $vendor_id }}">
                            <label class="form-label" for="payment-method">{{ __('messages.choose_payment_method') }}:</label>
                            <select id="payment-method" name="payment_method" class="form-control select2">
                                @php
                                    $payment_methods = [
                                        'str_payment_method' => 'stripe',
                                        'razor_payment_method' => 'razorpay',
                                        'paystack_payment_method' => 'paystack',
                                        'paypal_payment_method' => 'paypal',
                                        'flutterwave_payment_method' => 'flutterwave',
                                        'cinet_payment_method' => 'cinet',
                                        'sadad_payment_method' => 'sadad',
                                        'airtel_payment_method' => 'airtel',
                                        'phonepe_payment_method' => 'phonepe',
                                        'midtrans_payment_method' => 'midtrans'
                                    ];
                                @endphp
                                @foreach ($payment_methods as $setting => $method)
                                    @if (setting($setting) == 1)
                                        <option value="{{ $method }}">{{ __('messages.' . $method) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    

                    <div class="mt-4">
                        <div class="payment-detail bg-body p-3 rounded">
                            <h6 class="font-size-18">{{__('messages.payment_details')}}</h6>
                            <div class="table-responsive">
                                <table class="table border-0 bg-body">
                                    <tbody>
                                        <tr class="border-0">
                                            <td>{{__('messages.price')}}</td>
                                            <td><h6 class="font-size-18 text-end mb-0" id="price"></h6></td>
                                        </tr>
                                        <tr class="discount d-none">
                                            <td>{{__('messages.discount')}}</td>
                                            <td><h6 class="font-size-18 text-end mb-0" id="discount"></h6></td>
                                        </tr> 
                                      
                                        <tr class="border-0">
                                            <td class="fw-600">{{__('messages.total')}}</td>
                                            <td><h6 class="font-size-18 text-end mb-0" id="total"></h6></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="mt-4">
                                    <div class="d-flex justify-content-between gap-3">
                                        <h6>{{__('messages.lbl_total_price')}}</h6>
                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                            <h5 class="mb-0 text-primary" id="total-payment"></h5>
                                            <small><del id="old-price"></del></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <i class="ph ph-shield-check text-success"></i>
                                        <h6 class="mb-0">{{__('messages.secure')}}</h6>
                                    </div>
                                    <button type="button" class="btn btn-primary " onclick="paymentSubmit()">{{__('messages.proceed_payment')}}</button>


                                <div class="modal fade" id="appliedTax" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div class="ph-circle-check text-primary font-size-140"></div>
                                                        <h5 class="font-size-28 mb-4">{{ __('messages.applied_taxes') }}</h5>

                                                        <div id="applied_tax">


                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="text-center d-none">
                                        <div class="ph-circle-check text-primary font-size-140"></div>
                                        <h5 class="font-size-28 mb-4">{{__('messages.thanks_for_payment')}}</h5>
                                        <p>{{__('messages.payment_success')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <i class="ph ph-lock-key text-primary"></i>
                                <p class="mb-0">{{__('messages.payment_secure')}}</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header justify-content-center"> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorModalMessage"></p>
            </div>
            <div class="modal-footer justify-content-center"> 
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>        



@include('layouts.script')
<script src="{{ asset('js/jquery.min.js') }}"></script> 
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ === "undefined") {
        console.error("jQuery is not loaded. Make sure to include jQuery before this script.");
        return;
    }
        @if(session('error'))
        $('#errorModalMessage').text('{{ session('error') }}');
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        @endif        
 
    var selectedPlanId = @json($planId); // Injected from backend
    
    if (selectedPlanId) {
        var selectedRadio = $('input[type="radio"][value="' + selectedPlanId + '"]');
        selectedRadio.prop('checked', true);
        $('#selected-plan-id').val(selectedPlanId);
        $('#selected-price').val(selectedRadio.data('amount'));
        updatePaymentDetails(selectedPlanId);

        // Highlight the selected plan
        selectedRadio.closest('.stripe-payment-form').addClass('active');
    }

    // When user clicks anywhere on the card (label), select the radio button
    $('.stripe-payment-form').on('click', function () {
        var radioInput = $(this).find('.payment-radio-btn');
        radioInput.prop('checked', true).trigger('change'); // Check & trigger event
    });

    $('.payment-radio-btn').on('change', function () {
        var selectedPrice = $(this).data('amount');
        var selectedPlanId = $(this).val();

        $('#selected-price').val(selectedPrice);
        $('#selected-plan-id').val(selectedPlanId);
        updatePaymentDetails(selectedPlanId);

        // Remove active class from all and add to the selected one
        $('.stripe-payment-form').removeClass('active');
        $(this).closest('.stripe-payment-form').addClass('active');
    });
    
        function updatePaymentDetails(planId) {
            $.ajax({
            url: `{{ route('get-payment-details') }}`,
                method: 'POST',
                data: {
                    plan_id: planId,                   
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#selected-price').val(response.subtotal);
                    $('#subtotal').text(currencyFormat(response.subtotal));
                    $('#total').text(currencyFormat(response.total));
                    $('#total-payment').text(currencyFormat(response.total));
                    $('#price').text(currencyFormat(response.price));
                    if (response.discount_amount > 0) {
                        $('#discount').text(currencyFormat(response.discount_amount));
                        $('.discount').removeClass('d-none'); // Show discount row
                        $('.subtotal').removeClass('d-none');
                        $('#subtotal').text(currencyFormat(response.subtotal)); // Ensure subtotal is shown
                    } else {
                        $('.discount').addClass('d-none'); // Hide discount row
                        $('.subtotal').addClass('d-none');
                        
                    }
                },
                error: function(xhr) {
                    $('#errorModalMessage').text('An error occurred while fetching payment details.');
                    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            });
        }
    
    

    });

function paymentSubmit(){
    let paymentForm=document.getElementById('payment-form')

        let formData = $(paymentForm).serialize(); // Serialized string
        const subscriptionPayment = @json($subscriptionType);
        formData += '&subscription_type=' + encodeURIComponent(subscriptionPayment);
            $.ajax({
                url: $(paymentForm).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    const errorResponse = xhr.responseJSON || {};
                    const errorMessage = errorResponse.error || 'An error occurred. Please try another payment method.';
                    // Display an error modal using Bootstrap
                    $('#errorModalMessage').text(errorMessage);
                    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                }
            });
    
    }
    </script>