@extends('backend.layouts.app')
@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
<div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3 d-none payment">
    <a href="{{ route('backend.vendors.subscription-history') }}" class="btn btn-sm btn-primary">
        <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
    </a>
</div>
<div class="section-spacing-bottom">
    <div class="container" id="payment-container">
        <div class="page-title">
            <h4 class="m-0 text-center">{{ __('messages.subscription_plans') }}</h4>
        </div>

        <!-- Active Subscription Card -->
         <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between rounded p-4">
         
            <div class="row gy-4 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-3 mt-2">
                <!-- Subscription Plan Basic -->
                @foreach($plans as $plan)
                <div class="col">
                    <div class="card h-100 mb-0">
                        <div class="card-body">
                            <div class="subscription-plan-wrapper {{ $plan->id == $currentPlanId ? 'active' : '' }} rounded">
                                <p class="subscription-name text-uppercase">{{ $plan->name }}</p>
                                <div class="subscription-plan-header card border">
                                    <div class="card-body">
                                        @if($plan->discount == 1)
                                        <div class="discount-offer">{{$plan->discount_percentage}} % off</div>
                                        @endif
                                        <p class="subscription-price mb-0 text-primary">
                                            @if($plan->discount == 1)
                                            {{ Currency::format($plan->total_price) }}
                                            <s class="text-body">{{ Currency::format($plan->price) }}</s>
                                            @else
                                            {{ Currency::format($plan->price) }}
                                            @endif
                                            <span class="subscription-price-desc text-body fw-light">/ {{ $plan->duration_value }} {{ $plan->duration }}</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="line-count-3"> {!! $plan->description !!} </p>
                                <div class="readmore-wrapper">
                                    <ul class="list-inline subscription-details">
                                        @foreach ($plan->planLimitation as $limitation)
                                            @php
                                                // Set the default icon class for disabled state
                                                $iconClass = 'ph-x-circle text-danger';

                                                // Determine icon class based on specific conditions
                                                if ($limitation->limitation_value) {
                                                    $iconClass = 'ph-checks text-success'; // Show check for enabled limitations
                                                } elseif ($limitation->limitation_slug === 'device-limit' && $limitation->limit == 0) {
                                                    $iconClass = 'ph-checks text-success'; // Show check for 1 mobile device
                                                } elseif ($limitation->limitation_slug === 'profile-limit' && $limitation->limit == 0) {
                                                    $iconClass = 'ph-checks text-success'; // Show check for profile limit
                                                }
                                            @endphp

                                            <li class="list-desc d-flex align-items-start gap-3 mb-2">
                                                <i class="ph {{ $iconClass }} align-middle"></i>
                                                <span class="font-size-16">
                                                    @switch($limitation->limitation_slug)
                                                        
                                                        @case('profile-limit')
                                                            {{ __('messages.you_can_create_up_to') }} {{ $limitation->limit == 0 ? 1 : $limitation->limit }} {{ __('messages.profiles_on_this_plan_for_different_users') }}.
                                                            @break

                                                        @default
                                                        {{ ucwords(str_replace('-', ' ', $limitation->limitation_slug)) }}: 
{{ $limitation->limitation_value == 1 ? ($limitation->limit ?? __('messages.lbl_enable')) : __('messages.lbl_disable') }}

                                                @endswitch
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <button type="button"
                                        class="rounded btn btn-{{ $plan->id == $currentPlanId ? 'primary' : 'secondary' }} subscription-btn"
                                        data-plan-id="{{ $plan->id }}"
                                        data-plan-name="{{ $plan->name }}"
                                        data-vendor-id="{{$vendor_id}}">
                                        {{ $plan->id == $currentPlanId ? __('messages.Renew_plan') : __('messages.choose_plan') }}

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script> 
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ === "undefined") {
        console.error("jQuery is not loaded. Make sure to include jQuery before this script.");
        return;
    }

    $('.payment').removeClass('d-none');
    let appUrl = "{{ url('') }}"; 
    
    $('.subscription-btn').on('click', function() {
        var planId = $(this).data('plan-id');
        var planName = $(this).data('plan-name');
        var vendorId = $(this).data('vendor-id');
        var subscription_type = 'upgrade-plan';

        $.ajax({
            url: appUrl + `/select-plansubscribe`,
            method: 'POST',
            data: {
                plan_id: planId,
                plan_name: planName,
                vendor_id: vendorId,
                subscription_type: subscription_type,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#payment-container').empty();
                if(response.redirect_url){
                    window.location.href = response.redirect_url;
                }
            },
            error: function(xhr) {
                // Handle errors here
                alert("{{ __('messages.an_error_occurred_while_selecting_the_plan') }}");
            }
        });
    });
});
</script>
@endsection
