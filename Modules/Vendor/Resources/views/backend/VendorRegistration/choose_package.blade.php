<div class="section-spacing-bottom">
    <div class="title mb-4">
        <h3>{{ __('messages.choose_plan') }}</h3>
        <p>{{__('messages.explore_feature_create')}}</p>
    </div>
    <div id="payment-container">
        <div class="row gy-4 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-3">
            <!-- Subscription Plan Basic -->
            @foreach($plans as $plan)
            <div class="col">
                <div class="card bg-body h-100">
                    <div class="card-body">
                        <div class="subscription-plan-wrapper">
                            <h4 class="subscription-name">{{ $plan->name }}</h4>
                            <p class="line-count-2"> {!! $plan->description !!} </p>
                            <div class="subscription-plan-header card border mb-3">
                                <div class="card-body">
                                    @if($plan->discount == 1)
                                    <div class="discount-offer">{{$plan->discount_percentage}} % off</div>
                                    @endif
                                    <h4 class="subscription-price mb-0 text-primary">
                                        @if($plan->discount == 1)                                        
                                        {{ Currency::format($plan->total_price) }}
                                        <s class="text-body">{{ Currency::format($plan->price) }}/</s>
                                        @else
                                        {{ Currency::format($plan->price) }}
                                        @endif
                                        <small class="subscription-price-desc text-body fw-light">/ {{ $plan->duration_value }} {{ ucfirst($plan->duration) }}</small>
                                    </h4>
                                </div>
                            </div>
                            <div class="readmore-wrapper">
                                <ul class="list-inline subscription-details">
                                    @foreach ($plan->planLimitation as $limitation)
                                        @php
                                            // Set the default icon class for disabled state
                                            $iconClass = 'ph-x text-danger';

                                            // Determine icon class based on specific conditions
                                            if ($limitation->limitation_value) {
                                                $iconClass = 'ph-checks text-success'; // Show check for enabled limitations
                                            } elseif ($limitation->limitation_slug === 'device-limit' && $limitation->limit == 0) {
                                                $iconClass = 'ph-checks text-success'; // Show check for 1 mobile device
                                            } elseif ($limitation->limitation_slug === 'profile-limit' && $limitation->limit == 0) {
                                                $iconClass = 'ph-checks text-success'; // Show check for profile limit
                                            }
                                        @endphp

                                        <li class="list-desc d-flex align-items-center gap-3 mb-2">
                                            <i class="ph {{ $iconClass }} align-middle"></i>
                                            <span class="font-size-16">
                                                @switch($limitation->limitation_slug)
                                                    
                                                    @case('profile-limit')
                                                        You can create up to {{ $limitation->limit == 0 ? 1 : $limitation->limit }} profiles on this plan for different users.
                                                        @break

                                                    @default
                                                            {{ ucwords(str_replace('-', ' ', $limitation->limitation_slug)) }}: {{ $limitation->limitation_value == 1 ? $limitation->limit ?? 'enable'  : 'Disabled' }}
                                                @endswitch
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button"
                                    class="w-100 btn btn-secondary subscription-btn"
                                    data-plan-id="{{ $plan->id }}"
                                    data-plan-name="{{ $plan->name }}"
                                    data-vendor-id="{{$vendor_id}}">
                                
                                {{  'Choose Plan' }}
                            </button>
                        </div>
                    </div>
                </div>
                        
            </div>
            @endforeach
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
    let appUrl = "{{ url('') }}";  
 
    $('.subscription-btn').on('click', function() {
        var planId = $(this).data('plan-id');
        var planName = $(this).data('plan-name');
        var vendorId=$(this).data('vendor-id');
     
        $.ajax({
            url: appUrl +`/select-plan`, // Your route to handle plan selection
            method: 'POST',
            data: {
                plan_id: planId,
                plan_name: planName,
                vendor_id:vendorId,
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function(response) {
                $('#payment-container').empty();
                $('#payment-container').html(response.view); // Inject the view into a container
            },
            error: function(xhr) {
                // Handle errors here
                alert('An error occurred while selecting the plan.');
            }
        });
    });
});
</script>