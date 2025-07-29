<!-- Basic Info Card -->
<div>
    <h5 class="mb-2">{{ __('messages.vendor_details_info') }}</h5>
    <div class="card bg-body">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3">
                <!-- Profile Image Column -->
                <div class="avatar-wrapper">
                    <img src="{{ isset($data->profile_image) && $data->profile_image ? setBaseUrlWithFileName($data->profile_image) : '#' }}"
                        alt="{{ $data->first_name }}" class="avatar avatar-70 rounded-pill">
                </div>

                <!-- Info Column -->
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-md-nowrap flex-wrap">
                        <h4 class="mb-0">{{ $data->first_name ?? '--' }} {{ $data->last_name ?? '--' }}</h4>
                        <label class="badge bg-{{ $data->status ? 'success' : 'danger' }} rounded-pill">
                            {{ $data->status ? __('messages.active') : __('messages.inactive') }}
                        </label>
                    </div>

                    <div class="d-flex align-items-center column-gap-3 row-gap-2 mt-2 flex-md-nowrap flex-wrap">
                        <div class="d-flex align-items-center gap-1 text-break">
                            <i class="ph ph-envelope-simple"></i>
                            <a href="mailto:{{ $data->email }}" class="text-decoration-none text-body">
                                {{ $data->email ?? '--' }}
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <i class="ph ph-phone"></i>
                            <a href="tel:{{ $data->mobile }}" class="text-decoration-none text-body">
                                {{ $data->mobile ?? '--' }}
                            </a>
                        </div>

                        @if ($data->address || $data->city?->name || $data->state?->name || $data->country?->name)
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="ph ph-map-pin"></i>
                                                <span>{{ trim(implode(', ', array_filter([
                            $data->address ?? null,
                            $data->city->name ?? null,
                            $data->state->name ?? null,
                            $data->country->name ?? null,
                        ]))) }}
                                                </span>
                                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<div class="row">
    <div class="col-xl-4 col-md-6">
        <h5 class="mb-3">{{ __('messages.earning_and_tax') }}</h5>
        <div class="card bg-body">
            <div class="card-body">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="">{{ __('messages.total_earning') }}:</span>
                        <h6> {{ \Currency::format($statistics['pendingPayouts'] ?? 0) }}
                        </h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="">{{ __('messages.total_revenue') }}:</span>
                        <h6> {{ \Currency::format($statistics['paidPayouts'] ?? 0) }}
                        </h6>
                    </div>
                    <span class="fw-bold">{{ __('messages.lbl_taxes') }}:</span>
                    @forelse($data->userTaxMapping ?? [] as $taxMapping)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold m-0">
                                {{ optional($taxMapping->tax)->title ?? __('messages.no_tax_assigned') }}</h6>
                            <span>{{ optional($taxMapping->tax)->type == 'Percentage' ? optional($taxMapping->tax)->value . '%' : \Currency::format(optional($taxMapping->tax)->value ?? 0)}}</span>
                        </div>
                    @empty
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <h6 class="fw-bold m-0">{{ __('messages.no_tax_assigned') }}</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
       <h5 class="mb-3">{{ __('messages.lbl_commission') }}</h5>
       <div class="card bg-body">
           <div class="card-body">
               @if(Setting('vendor_commission_type') =='per_vendor' && optional($data->userCommissionMapping)->isNotEmpty())
                   @php
                       $hasActiveCommissions = false;
                   @endphp
                   
                   @foreach($data->userCommissionMapping as $commission)
                       @if($commission->commissions && $commission->commissions->status == 1 && !$commission->commissions->deleted_at)
                           @php
                               $hasActiveCommissions = true;
                           @endphp
                           <div class="d-flex justify-content-between">
                               <span class="fw-bold">{{ $commission->commissions->title ?? '-' }}</span>
                               <span class="fw-bold">
                                   {{ ($commission->commission_type == 'Percentage' ? $commission->commission . '%' : \Currency::format($commission->commission)) ?? 0 }}
                               </span>
                           </div>
                       @endif
                   @endforeach

                   @if(!$hasActiveCommissions)
                       <p class="text-center">{{ __('messages.no_active_commission_data_available') }}</p>
                   @endif
               @elseif(Setting('vendor_commission_type') =='global')
                   @foreach ($commissions as $commission) 
                       @if($commission && $commission->status == 1 && !$commission->deleted_at)
                               @php
                                   $hasActiveCommissions = true;
                               @endphp
                               <div class="d-flex justify-content-between">
                                   <span class="fw-bold">{{ $commission->title ?? '-' }}</span>
                                   <span class="fw-bold">
                                       {{ ($commission->type == 'Percentage' ? $commission->value . '%' : \Currency::format($commission->value)) ?? 0 }}
                                   </span>
                               </div>
                           @endif
                   @endforeach
               @else
                   <p class="text-center">{{ __('messages.no_commission_data_available') }}</p>
               @endif
           </div>
       </div>
   </div>
    <div class="col-xl-4">
        @php 
        use Illuminate\Support\Facades\Crypt;
        $encryptedVendorId = Crypt::encryptString($data->id);
        @endphp

            <!-- After the statistics cards and before basic info -->
        @if(!empty($data->subscriptiondata))
            @php
                $subscription = $data->subscriptiondata->where('status', 'active')->first();
                $limitations = null;
                if (!empty($subscription)) {
                    $limitations = json_decode($subscription->plan_type, true); // Convert JSON to an associative array
                }
            @endphp

            @if($subscription)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">{{ __('messages.current_plan') }}</h5>
                    <a href="{{ route('backend.vendors.subscription-history', ['vendor_id' => $encryptedVendorId]) }}" class="text-primary text-decoration-none">
                        {{ __('messages.view_history') }}
                    </a>
                </div>
                <div class="card bg-body">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-md-center mb-3 flex-md-row flex-column gap-2">
                            <div>
                                <h6 class="mb-1">{{ $subscription->name ?? '-' }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0 text-primary">{{ Currency::format($subscription->total_amount ?? 0)  }}</h4>
                                    <span>/ {{ $subscription->duration ?? '-' }} {{ $subscription->type ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($subscription)
                                    <span>{{ __('messages.expiring_on') }}:
                                        {{ \App\Models\Setting::formatDate($subscription->end_date) }}
                                    </span>
                                    <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : 'danger' }}">
                                        {{ $subscription->status == 'active' ? __('messages.active') : __('messages.inactive') }}
                                    </span>
                                @else
                                    <span>{{ __('messages.no_active_subscription') }}</span>
                                    <span class="badge bg-warning">{{ __('messages.not_subscribed') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
  
</div>

