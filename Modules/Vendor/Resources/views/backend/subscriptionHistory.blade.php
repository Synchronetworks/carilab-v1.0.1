@extends('backend.layouts.app')
@section('title')
    {{ __($module_title) }}
@endsection
@section('content')

<div class="section-spacing">
   
 
    @if(auth()->user()->hasRole('vendor'))
      <!-- Subscription Plan Section -->
        <div class="upgrade-plan d-flex flex-wrap gap-3 align-items-center justify-content-between rounded p-4 bg-warning-subtle border border-warning">
            <div class="d-flex align-items-center gap-4">
                <i class="ph ph-crown text-warning fs-2"></i>
                <div>
                    @if($activeSubscriptions)
                        <h6 class="super-plan fw-bold mb-1">{{ $activeSubscriptions->name }}</h6>
                        <p class="mb-0 text-secondary small">
                            {{ __('messages.expiring_on') }} {{ \Carbon\Carbon::parse($activeSubscriptions->end_date)->format('d F, Y') }}
                        </p>
                        @else
                        <h6 class="super-plan">{{__('messages.no_active_plan')}}</h6>
                        <p class="mb-0 text-body">{{__('messages.not_active_subscription')}}</p>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-3">
                @if($activeSubscriptions)
                    <a href="{{ route('subscriptionPlan') }}" class="btn btn-light">{{__('messages.upgrade')}}</a>
                    <button type="button" class="btn btn-primary"  data-subscription-id="{{ $activeSubscriptions->id }}" data-bs-toggle="modal" data-bs-target="#CancleSubscriptionModal">{{__('messages.cancel')}}</button>
                @else
                    <a href="{{ route('subscriptionPlan') }}" class="btn btn-light">{{__('messages.subscribe')}}</a>
                @endif
            </div>
        </div>
    @endif
    <div class="mt-4">

      <h5 class="main-title text-capitalize mb-2">{{__('messages.payment_history')}} </h5>
        <div class="table-responsive">
          <table class="table payment-history table-borderless">
            <thead class="table-dark">
              <tr>
                <th class="text-white">{{__('messages.date')}}</th>
                <th class="text-white">{{__('messages.plan')}}</th>
                <th class="text-white">{{__('messages.lbl_duration')}}</th>
                <th class="text-white">{{__('messages.lbl_expiry_date')}}</th>
                <th class="text-white">{{__('messages.lbl_amount')}}</th>
                <th class="text-white">{{__('messages.lbl_discount')}}</th> 
                <th class="text-white">{{__('messages.total')}}</th>
                <th class="text-white">{{__('messages.lbl_payment_method')}}</th>
                <th class="text-white">{{__('messages.lbl_status')}}</th>
              </tr>
            </thead>
            <tbody class="payment-info">
                @if($subscriptions->isEmpty())
                <tr>
                    <td colspan="10" class="text-center text-white fw-bold">
                        {{ __('messages.subscription_history_not_found') }} <!-- You can customize this message -->
                    </td>
                </tr>
            @else
                <tbody class="payment-info">
                    @foreach($subscriptions->sortByDesc('start_date') as $subscription)
                    <tr> 
                        <td class="fw-bold ">{{ $subscription->start_date ? App\Models\Setting::formatDate($subscription->start_date) : '-' }}</td>
                        <td class="fw-bold ">{{ $subscription->name }}</td>
                        <td class="fw-bold ">{{ $subscription->duration }} {{ $subscription->type }}</td>
                        <td class="fw-bold ">{{ $subscription->end_date ? App\Models\Setting::formatDate($subscription->end_date) : '-' }}</td>
                        <td class="fw-bold ">{{  Currency::format($subscription->amount) }}</td>
                        <td class="fw-bold "> {{ $subscription->discount_percentage ? $subscription->discount_percentage . ' % off' :  Currency::format(0) }}</td>
                        <td class="fw-bold ">{{  Currency::format($subscription->total_amount) }}</td>
                        <td class="fw-bold ">{{ ucfirst($subscription->subscription_transaction->payment_type ?? '-') }}</td>
                        <td class="fw-bold {{ $subscription->status == 'active' ? 'text-success' : 'text-danger' }}">
                            @if($subscription->status == 'active')
                                {{__('messages.active')}}
                            @elseif( $subscription->status == 'inactive')
                                {{__('messages.inactive')}}
                            @else
                                {{__('messages.cancle')}}
                            @endif
                        </td>
 
                    </td>                                
                        
                    </tr>
                    @endforeach
                </tbody>
                @endif
            </tbody>
          </table>
        </div>
    </div>

  <div class="modal fade" id="CancleSubscriptionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-acoount-card">
                    <div class="modal-content position-relative">
                        <button type="button" class="btn btn-primary custom-close-btn rounded-2" data-bs-dismiss="modal">
                        <i class="ph ph-x text-white fw-bold align-middle"></i>
                        </button>
                    <div class="modal-body modal-acoount-info text-center">
                        <h6 class="mt-3 pt-2">{{__('messages.cancle_subscription')}}</h6>
                        <div class="d-flex justify-content-center gap-3 mt-4 pt-3">
                            <button type="button" class=" btn btn-dark" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                            <button type="button" class="btn btn-primary" onclick="cancelSubscription()">{{__('messages.proceed')}}</button>

                        </div>
                    </div>
                    </div>
                </div>
            </div>
            @if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            showMessage("{{ session('success') }}");
        });
    </script>
@endif
            <script>
 function showMessage(message) {
            Snackbar.show({
                text: message,
                pos: 'bottom-left'
            });
        }
let baseUrl =  "{{ url('') }}";

function cancelSubscription() {

    const subscriptionId = document.querySelector('[data-bs-target="#CancleSubscriptionModal"]').getAttribute('data-subscription-id');

        fetch(`{{ route('backend.vendors.cancelSubscription') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: subscriptionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.successSnackbar('Your subscription has been canceled.');
                location.reload();
            } else {

            }
        })
        .catch(error => {
            console.error('Error:', error);

        });

}


</script>
@endsection