<div class="page-content">
    <!-- Statistics Cards -->
    <div class="card">
        <div class="card-body">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 gy-4">
                @php
                    $isMultivendor = multivendor() == 1;
                    $colClass = $isMultivendor ? 'col' : 'col';
                @endphp
            
                <div class="{{ $colClass }}">
                    <div class="card bg-body m-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="m-0 text-primary">{{ \Currency::format($totlearning ?? 0) }}</h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-currency-circle-dollar"></i>
                                </div>
                            </div>
                            <h6 class="mt-3 mb-0">{{ __('messages.total_earning') }}</h6>
                        </div>
                    </div>
                </div>
            
                <div class="{{ $colClass }}">
                    <div class="card bg-body m-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="card-text text-primary">{{ \Currency::format($statistics['paidPayouts']) ?? 0 }}</h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-hand-coins"></i>
                                </div>
                            </div>
                            <h6 class="mt-3 mb-0">{{ __('messages.total_payout') }}</h6>
                        </div>
                    </div>
                </div>
            
                <div class="{{ $colClass }}">
                    <div class="card bg-body m-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="card-text text-primary">{{ \Currency::format($statistics['pendingPayouts']) ?? 0 }}</h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-coins"></i>
                                </div>
                            </div>
                            <h6 class="mt-3 mb-0">{{ __('messages.total_pending_payouts') }}</h6>
                        </div>
                    </div>
                </div>
            
                <div class="{{ $colClass }}">
                    <div class="card bg-body m-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="card-text text-primary">{{ $statistics['total_appointments'] ?? 0 }}</h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-users-three"></i>
                                </div>
                            </div>
                            <h6 class="mt-3 mb-0">{{__('messages.lbl_total_appointments')}}</h6>
                        </div>
                    </div>
                </div>
            
               
            </div>
            <!-- Basic Info Card -->
            <div class="mt-4">
                <h5 class="mb-3">{{__('messages.collector')}} {{__('messages.basic_information')}}</h5>
                <div class="card bg-body">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap">
                            <!-- Profile Image Column -->
                            <div class="avatar-wrapper">
                                <img src="{{ setBaseUrlWithFileName($data->profile_image) }}" alt="{{ $data->first_name }}" class="avatar avatar-60 rounded-circle">
                            </div>
                            <!-- Info Column -->
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <a href="{{ route('backend.collectors.details', $data->id) }}" >
                                        <h4 class="mb-0">{{ $data->first_name ?? '--' }} {{ $data->last_name ?? '--' }}</h4>
                                    </a>
                                    <label class="badge bg-{{ $data->status ? 'success' : 'danger' }} rounded-pill">
                                        {{ $data->status ? __('messages.active') : __('messages.inactive') }}
                                    </label>
                                </div>

                                <div class="d-flex align-items-center flex-wrap row-gap-1 column-gap-4 mt-2">
                                    <div class="d-flex align-items-center gap-2 text-break">
                                        <i class="ph ph-envelope-simple"></i>
                                        <a href="mailto:{{ $data->email }}" class="text-decoration-none text-body text-break">
                                            {{ $data->email ?? '--' }}
                                        </a>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ph ph-phone"></i>
                                        <a href="tel:{{ $data->mobile }}" class="text-decoration-none text-body">
                                            @php
                                                $phone = $data->mobile;
                                                $formattedPhone = isset($phone) && preg_match('/^(\d+)-(.+)$/', $phone, $matches) 
                                                    ? '+' . $matches[1] . $matches[2] 
                                                    : ($phone ?? '--');
                                            @endphp
                                            {{ $formattedPhone }}
                                        </a>
                                    </div>

                                    @if(!empty($data->address))
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ph ph-map-pin"></i>
                                        <span>{{ trim(
                                            implode(
                                                ', ',
                                                array_filter([
                                                    $data->address ?? null,
                                                    $data->city->name ?? null,
                                                    $data->state->name ?? null,
                                                    $data->country->name ?? null,
                                                    $data->postal_code ?? null,
                                                ]),
                                            ),
                                        ) ?:
                                            '--' }}
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

                @if($data->lab && $data->lab->lab)
                    <div class="col-xl-4 col-md-6">
                        <h5 class="mb-3">{{ __('messages.lab_basic_information') }}</h5>
                        <div class="card bg-body">
                            <div class="card-body">
                                <div class="d-flex gap-3 flex-sm-nowrap flex-wrap">
                                    <!-- Profile Image Column -->
                                    <div class="avatar-wrapper">
                                        <img src="{{ $data->lab->lab->getLogoUrlAttribute() ?? asset('default-avatar.png') }}" alt="{{ $data->lab->lab->name }}" class="avatar avatar-60 rounded-pill">
                                    </div>
                                    <!-- Info Column -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-start justify-content-between gap-1 flex-wrap">
                                            <div class="d-flex flex-column gap-1">
                                                <a href="{{ route('backend.labs.details', $data->lab->lab->id) }}" >
                                                <h4 class="mb-0">{{ $data->lab->lab->name ?? '--' }}</h4></a>
                                                <a href="mailto:{{ $data->lab->lab->email ?? '--' }}" class="text-decoration-none  text-break text-body">
                                                    {{ $data->lab->lab->email ?? '--' }}
                                                </a>
                                                <a href="tel:{{ $data->lab->lab->phone_number ?? '--' }}" class="text-decoration-none text-body">
                                                        @php
                                                        $phone = $data->lab->lab->phone_number;
                                                        $formattedPhone = preg_match('/^(\d+)-(.+)$/', $phone, $matches) 
                                                            ? '+' . $matches[1] . $matches[2] 
                                                            : $phone;
                                                    @endphp
                                                    {{ $formattedPhone }}
                                                
                                                    </a>
                                            </div>
                                            <label class="badge bg-{{ $data->lab->lab->status ? 'success' : 'danger' }} rounded-pill">
                                                {{ $data->lab->lab->status ? __('messages.active') : __('messages.inactive') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            
                <!-- Profile Information -->
                @if($data->collectorVendormapping && $data->collectorVendormapping->vendor)
                    <div class="col-xl-4 col-md-6">
                        <!-- Basic Info Card -->
                        <h5 class="mb-3">{{ __('messages.vendor_basic_information') }}</h5>
                        <div class="card bg-body">
                            <div class="card-body">
                                <div class="d-flex gap-3 flex-sm-nowrap flex-wrap">
                                    <!-- Profile Image Column -->
                                    <div class="avatar-wrapper">
                                        <img src="{{ setBaseUrlWithFileName($data->collectorVendormapping->vendor->profile_image) }}" alt="{{ $data->collectorVendormapping->vendor->full_name }}" class="avatar avatar-60 rounded-pill">                               
                                    </div>

                                    <!-- Info Column -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-start justify-content-between gap-1 flex-wrap">
                                            <div class="d-flex flex-column gap-1">
                                                <a href="{{ route('backend.vendors.details', $data->collectorVendormapping->vendor->id) }}" >
                                                    <h4 class="mb-0">{{ $data->collectorVendormapping->vendor->full_name ?? '--' }}</h4>
                                                </a>
                                                <a href="mailto:{{ $data->collectorVendormapping->vendor->email ?? '--' }}" class="text-break text-decoration-none text-body text-break">
                                                    {{ $data->collectorVendormapping->vendor->email ?? '--' }}
                                                </a>
                                                <a href="tel:{{ $data->collectorVendormapping->vendor->mobile ?? '--' }}" class="text-decoration-none text-body">
                                                    @php
                                                    $phone = $data->collectorVendormapping->vendor->mobile;
                                                    $formattedPhone = isset($phone) && preg_match('/^(\d+)-(.+)$/', $phone, $matches) 
                                                        ? '+' . $matches[1] . $matches[2] 
                                                        : ($phone ?? '--');
                                                    @endphp
                                                    {{ $formattedPhone }}
                                                </a>
                                            </div>
                                            <label class="badge bg-{{ $data->collectorVendormapping->vendor->status ? 'success' : 'danger' }} rounded-pill">
                                                {{ $data->collectorVendormapping->vendor->status ? __('messages.active') : __('messages.inactive') }}
                                            </label>
                                        </div>                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-xl-4 col-md-6">
                    <h5 class="mb-3">{{ __('messages.lbl_commission') }}</h5>
                    <div class="card bg-body">
                        <div class="card-body">
                            @if(Setting('collector_commission_type') == 'per_collector' && optional($data->userCommissionMapping)->isNotEmpty())
                                @foreach($data->userCommissionMapping as $commission)
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">{{ $commission->commissions->title ?? '-' }}</span>
                                        <span class="fw-bold">
                                            {{ ($commission->commission_type == 'Percentage' ? $commission->commission . '%' : \Currency::format($commission->commission)) ?? 0 }}
                                        </span>
                                    </div>
                                @endforeach
                            @elseif(Setting('collector_commission_type') =='global')
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

            </div>
          

        </div>
    </div>
</div>