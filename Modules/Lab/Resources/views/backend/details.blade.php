@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection



@section('content')
    <div class="page-content">
        <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
            <a href="{{ route('backend.labs.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <!-- Tabs -->
        <div class="d-flex align-items-center gap-2 mb-4">
            
            <div class="custom-tab-slider">
                <ul class="nav nav-pills" id="labTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview"
                            role="tab">{{__('messages.overview')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="appointments-tab" data-bs-toggle="tab" href="#appointments"
                            role="tab">{{__('messages.appointments')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="payments-tab" data-bs-toggle="tab" href="#payments" role="tab">{{__('messages.payment_history')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="revenue-tab" data-bs-toggle="tab" href="#revenue" role="tab">{{__('messages.revenue_details')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab">{{__('messages.reviews')}}</a>
                    </li>
                </ul>
            </div>
     
        </div>

        <!-- Tab Content -->
        <div class="tab-content mt-4">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview">
                <div class="card">
                    <div class="card-body">
                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class="text-primary">
                                                {{ $totaltescase }}
                                            </h3>
                                            <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                                <i class="ph ph-clipboard-text"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.total_test_case')}}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class="text-primary">{{ \Currency::format($totalRevenue ?? 0) }}</h3>
                                            <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                                <i class="ph ph-currency-circle-dollar"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.total_revenue')}}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class="text-primary">{{ $totalAppointments ?? 0 }}</h3>
                                            <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                                <i class="ph ph-calendar-dots"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.lbl_total_appointments')}}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class="text-primary">{{ \Currency::format($totalpendingpayouts ?? 0 )}}</h3>
                                            <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                                <i class="ph ph-hand-coins"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.total_pending_payouts')}}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-9">
                                 <!-- Lab Basic Information -->
                                <h5 class="mb-3">{{__('messages.lab_basic_information')}}</h5>
                                <div class="card bg-body m-0">
                                    <div class="card-body">
                                        <div class="d-flex flex-sm-nowrap flex-wrap gap-3">
                                            <!-- Profile Image Column -->
                                            <div class="avatar-wrapper">
                                                <img src="{{ $data->getLogoUrlAttribute() != null ? $data->getLogoUrlAttribute() : setBaseUrlWithFileName() }}"
                                                    alt="avatar" class="avatar avatar-60 rounded-pill">
                                            </div>

                                            <!-- Info Column -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                                    <a href="{{ route('backend.labs.details', $data->id) }}" >
                                                        <h4 class="mb-0">{{ $data->name ?? '--' }}</h4>
                                                    </a>
                                                    <label class="badge bg-{{ $data->status ? 'success' : 'danger' }} rounded-pill">
                                                        {{ $data->status ? __('messages.active') : __('messages.inactive') }}
                                                    </label>
                                                </div>
                                                <div>
                                                    <h6>{!! $data->description ?? '' !!}</h6>
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center gap-3">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="ph ph-envelope-simple"></i>
                                                        <a href="mailto:{{ $data->email }}" class="text-decoration-none text-body">
                                                                {{ $data->email ?? '--' }}
                                                            </a>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="ph ph-phone"></i>
                                                        <a href="tel:{{ $data->phone_number }}"
                                                                class="text-decoration-none text-body">
                                                                {{ $data->phone_number ?? '--' }}
                                                            </a>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="ph ph-map-pin"></i>
                                                        <span>{{ trim(
                                                                implode(
                                                                    ', ',
                                                                    array_filter([
                                                                        $data->address_line_1 ?? null,
                                                                        $data->address_line_2 ?? null,
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            @if($data->vendor && auth()->user()->user_type !== 'vendor')
                                <div class="col-lg-3">
                                    <!-- Basic Info Card -->
                                    <h5 class="mb-3">{{__('messages.vendor_basic_information')}}</h5>
                                    <div class="card bg-body m-0">
                                        <div class="card-body">
                                            <div class="d-flex gap-3 flex-wrap">
                                                <!-- Profile Image Column -->
                                                <div class="avatar-wrapper">
                                                    <img src="{{ setBaseUrlWithFileName($data->vendor->profile_image) }}" alt="{{ $data->vendor->full_name }}" class="avatar avatar-60 rounded-pill">
                                                </div>
    
                                                <!-- Info Column -->
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                                                        <div class="d-flex flex-column gap-1 ">
                                                            <a href="{{ route('backend.vendors.details', $data->vendor->id) }}" >
                                                            <h4 class="mb-0">{{ $data->vendor->full_name ?? '--' }}</h4>
                                                            </a>
                                                            <a href="mailto:{{ $data->vendor->email ?? '--' }}" class="text-break text-decoration-none text-body text-break">
                                                                {{ $data->vendor->email ?? '--' }}
                                                            </a>
    
                                                            <a href="tel:{{ $data->vendor->mobile ?? '--' }}" class="text-decoration-none text-body">
                                                                {{ $data->vendor->mobile ?? '--' }}
                                                            </a>
                                                        </div>
                                                        <label class="badge bg-{{ $data->vendor->status ? 'success' : 'danger' }} rounded-pill">
                                                            {{ $data->vendor->status ? __('messages.active') : __('messages.inactive') }}
                                                        </label>
                                                    </div>
                                                        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                                              

                        @if ($data->labSessions->count() > 0)
                        <h5 class="mt-4 mb-2">{{__('messages.lab_sessions')}}</h5>
                        <div class="card bg-body mb-0">
                            <div class="card-body">
                                <div class="row gy-4">
                                    @foreach ($data->labSessions as $session)
                                    <div class="col-md-6 col-lg-3">
                                        <div class="card mb-0">
                                            <div class="p-3 rounded card-body">
                                                <div class="d-flex align-items-center justify-content-between gap-3">
                                                    <h6 class="fw-bold mb-1">{{ ucfirst($session->day) }}</h6>
                                                    <span class="badge bg-{{ $session->is_holiday ? 'danger-subtle' : 'success-subtle' }}">
                                                        {{ $session->is_holiday ? __('messages.holiday') : __('messages.open') }}
                                                    </span>
                                                </div>                                                   
                                                <p class="mt-3 mb-0">
                                                    <i class="ph ph-clock"></i>
                                                    {{ App\Models\Setting::formatTime($session->start_time) }} - {{ App\Models\Setting::formatTime($session->end_time) }}
                                                </p>
                                                
                            
                                                <!-- Display Breaks if Available -->
                                                @php
                                                    $breaks = is_string($session->breaks) ? json_decode($session->breaks, true) : [];
                                                @endphp
                                                @if (!empty($breaks) && is_array($breaks))
                                                    <div class="mt-2">
                                                        <p class="mb-1 fw-bold">Breaks:</p>
                                                        @foreach ($breaks as $break)
                                                            @if (isset($break['start_break']) && isset($break['end_break']))
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="text-muted"><i class="ph ph-clock"></i> {{ App\Models\Setting::formatTime($break['start_break']) }}</span>
                                                                    <span class="text-muted"><i class="ph ph-clock"></i> {{ App\Models\Setting::formatTime($break['end_break']) }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>   
                            </div>
                        </div>
                        @endif

                            <!-- License & Accreditation -->
                        <h5 class="mb-2 mt-4">{{__('messages.license_accreditation_details')}}</h5>
                        <div class="card bg-body">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <!-- Column 1 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <p class="m-0">{{__('messages.license_number')}}: </p>
                                            <h6 class="mb-0">{{ $data->license_number ?? '--' }}</h6>
                                        </div>
                                    </div>

                                    <!-- Column 2 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <p class="m-0">{{__('messages.accreditation_type')}}:</p>
                                            <h6 class="mb-0">{{ $data->accreditation_type ?? '--' }}</h6>
                                        </div>
                                    </div>

                                    <!-- Column 3 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <p class="m-0">{{__('messages.license_expiry_date')}}: </p>
                                            <h6 class="mb-0">
                                                {{ optional($data->license_expiry_date)->format('M d, Y') ?? '--' }}</h6>
                                        </div>
                                    </div>

                                    <!-- Column 4 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <p class="mb-0">{{__('messages.license_document')}}: </p>
                                            @if ($data->getFirstMediaUrl('license_document'))
                                                <a href="{{ $data->getFirstMediaUrl('license_document') }}"
                                                    target="_blank" class=" text-primary fw-medium">
                                                    <i class="fas fa-file-pdf me-1"></i> {{__('messages.view_document')}}
                                                </a>
                                            @else
                                                <h6 class="m-0">{{__('messages.no_document_uploaded')}}</h6>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Column 5 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <p class="m-0">{{__('messages.accreditation_certificate')}}: </p>
                                            @if ($data->getFirstMediaUrl('accreditation_certificate'))
                                                <a href="{{ $data->getFirstMediaUrl('accreditation_certificate') }}"
                                                    target="_blank" class="text-primary fw-medium">
                                                    <i class="fas fa-certificate me-1"></i>{{__('messages.view_certificate')}}
                                                </a>
                                            @else
                                                <h6 class="m-0">{{__('messages.no_certificate_uploaded')}}</h6>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Column 6 -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <p class="m-0">{{__('messages.accreditation_expiry_date')}} :</p>
                                            <h6 class="mb-0">
                                                {{ optional($data->accreditation_expiry_date)->format('M d, Y') ?? '--' }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tax & Payment Details -->
                        <h5 class="mb-2 mt-4">{{__('messages.tax_payment_details')}}</h5>
                        <div class="card bg-body mb-0">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <!-- Column 1: Tax Information -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <p class="m-0">{{__('messages.tax_identification_number')}}:</p>
                                            <h6 class="mb-0">{{ $data->tax_identification_number ?? '--' }}</h6>
                                        </div>
                                    </div>

                                    <!-- Column 2: Applied Taxes -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <p class="m-0">{{__('messages.taxes_applied')}}:</p>
                                            @if ($data->labTaxMapping && $data->labTaxMapping->count() > 0)
                                                @foreach ($data->labTaxMapping as $taxMapping)
                                                    <div class="d-flex align-items-center">
                                                        <span class="h6 m-0 tax-value">
                                                            <span>{{ $taxMapping->tax->title }}</span>     {{ $taxMapping->tax->type == 'Percentage' ? $taxMapping->tax->value . '%' : \Currency::format($taxMapping->tax->value) }}
                                                        </span>
                                                        
                                                    </div>
                                                @endforeach
                                            @elseif (optional($data->vendor)->userTaxMapping && optional($data->vendor)->userTaxMapping->count() > 0)
                                            @foreach (optional($data->vendor)->userTaxMapping as $taxMapping)
                                                <div class="d-flex align-items-center mb-2">
                                                    
                                                    <span class="h6 mb-0tax-value">
                                                        {{ $taxMapping->tax->title }} {{ $taxMapping->tax->type == 'Percentage' ? $taxMapping->tax->value . '%' : \Currency::format($taxMapping->tax->value) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            @else
                                                <h6 class="mb-0">{{__('messages.no_taxes_configured')}}</h6>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Column 3: Payment Modes -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <p class="m-0">{{__('messages.payment_modes_accepted')}}:</p>
                                            <div class="d-flex flex-wrap gap-2">
                                                @php
                                                    $paymentModes = is_array($data->payment_modes)
                                                        ? $data->payment_modes
                                                        : explode(',', $data->payment_modes ?? '');
                                                    $paymentGateways = is_array($data->payment_gateways)
                                                        ? $data->payment_gateways
                                                        : explode(',', $data->payment_gateways ?? '');

                                                    // Check if 'online' exists in payment_modes
                                                    $hasOnlinePayment = in_array('online', $paymentModes);

                                                    // Define human-readable names for payment gateways
                                                    $gatewayNames = [
                                                        'stripe' =>  __('messages.lbl_stripe') ,
                                                        'razorpay' =>  __('messages.lbl_razorpay') ,
                                                        'paypal' =>  __('messages.lbl_paypal') ,
                                                        'bank_transfer' => __('messages.bank_transfer'),
                                                    ];
                                                @endphp

                                                @foreach ($paymentModes as $mode)
                                                    <span class="h6 m-0">{{ ucfirst(trim($mode)) }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Show Payment Gateways Only If 'Online' Mode Exists --}}
                                    @if ($hasOnlinePayment)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <p class="m-0">{{ __('messages.online_payment_gateways') }}:</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($paymentGateways as $gateway)
                                                        @php
                                                            $formattedGateway = ucfirst(
                                                                trim(str_replace('Payment', '', $gateway)),
                                                            );
                                                        @endphp
                                                        <span class="h6 m-0">{{ $gatewayNames[$formattedGateway] ?? $formattedGateway }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>                      

                    </div>
                </div>
            </div>

            <!-- Appointments Tab -->
            <div class="tab-pane fade" id="appointments">
                <div class="card">
                    <div class="card-body">
                        <!-- Appointment Statistics Cards -->
                        <div class="row gy-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class=" text-primary">{{ $totalAppointments ?? 0 }}</h3>
                                            <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                                <i class="ph ph-calendar-dots"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.lbl_total_appointments')}}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class="text-primary">{{ $cancelledAppointments ?? 0 }}</h3>
                                            <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                                <i class="ph ph-calendar-x"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.cancelled_appointments')}}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class=" text-primary">{{ $completedAppointments ?? 0 }}</h3>
                                            <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                                <i class="ph ph-calendar-check"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.completed_appointments')}}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card bg-body">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <h3 class=" text-primary">{{ $upcomingAppointments ?? 0 }}</h3>
                                            <div class="icon-50 rounded-circle bg-primary text-center text-white">
                                                <i class="ph ph-spinner-gap align-middle fs-3"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{__('messages.upcoming_appointments')}}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Info Card -->
                        <div id="appointment-table-container">
                            <!-- Table will be dynamically inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Tab -->
            <div class="tab-pane fade" id="payments">
                <!-- Payment Statistics Card -->
                <div class="card bg-body">
                    <div class="card-body">
                        <div class="d-inline-flex justify-content-between align-items-center bg-primary-subtle py-3 px-3 px-lg-5 rounded mb-4 gap-3 gap-lg-5">
                            <h6 class="m-0 heading-color">{{__('messages.total_revenue_generated')}}</h6>
                            <h3 class="mb-0 text-primary">{{ \Currency::format($totalRevenue) }}</h3>
                        </div>
                        <div id="payment-table-container">
                            <!-- Table will be dynamically inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Details Tab -->
            <div class="tab-pane fade" id="revenue">
                <!-- Revenue Statistics Cards -->
                <div class="row mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="bg-primary-subtle py-3 px-3 px-lg-5 rounded d-inline-flex justify-content-between align-items-center gap-3 gap-lg-5 mb-4">
                                <h6 class="m-0 heading-color">{{__('messages.total_revenue_generated')}}</h6>
                                <h3 class="mb-0 text-primary">{{ \Currency::format($totalRevenue) }}</h3>
                            </div>
                            <div id="revenue-table-container">
                                <div class="col-md-12">
                                    <div class="card card-stats card-block card-height">
                                        <div
                                            class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <h3 class="card-title">{{ __('messages.lbl_total_revenue') }}</h3>
                                            <div class="dropdown">
                                                <button class="btn btn-dark dropdown-toggle total_revenue" type="button"
                                                    id="dropdownTotalRevenue" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {{ __('messages.year') }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-soft-primary sub-dropdown"
                                                    aria-labelledby="dropdownTotalRevenue">
                                                    <li><a class="revenue-dropdown-item dropdown-item"
                                                            data-type="Year">{{ __('messages.year') }}</a></li>
                                                    <li><a class="revenue-dropdown-item dropdown-item"
                                                            data-type="Month">{{ __('messages.month') }}</a></li>
                                                    <li><a class="revenue-dropdown-item dropdown-item"
                                                            data-type="Week">{{ __('messages.week') }}</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="chart-top-revenue"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-pane fade" id="reviews">
                <div class="card">
                    <div class="card-body">
                        <!-- Rating Statistics Cards -->
                        <div class="row gy-4 mb-3">
                            <div class="col-xl-3 col-md-6">
                                <div class="card flex-grow-1 bg-body h-100">
                                    <div class="card-body ">
                                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                            <p class="mb-3">{{__('messages.total_reviews')}}</p>                                              
                                            <h1 class="display-6 m-0">
                                                {{ $totalReviews ?? 0 }} {{ __('messages.rating_review') }}
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card flex-grow-1 bg-body h-100">
                                    <div class="card-body ">
                                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                            <p class="mb-3">{{__('messages.average_rating')}}:</p>
                                            <div class="d-flex align-items-center justify-content-center gap-3">   
                                                <h1 class="display-6 m-0"> 
                                                    <i class="fas fa-star text-warning px-3"></i>{{ number_format($averageRating ?? 0, 1) }}/5.0
                                                </h1>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- Rating Distribution Card -->
                            <div class="col-xl-6 col-md-12">
                                <div class="card flex-grow-1 bg-body h-100">
                                    <div class="card-body">
                                        <div class="ratting-card">   
                                            @php
                                                $ratingLabels = [
                                                    5 => ['label' => __('messages.excellent'), 'class' => 'bg-success'],
                                                    4 => ['label' => __('messages.good'), 'class' => 'bg-primary'],
                                                    3 => ['label' => __('messages.average'), 'class' => 'bg-info'],
                                                    2 => ['label' => __('messages.below_average'), 'class' => 'bg-warning'],
                                                    1 => ['label' => __('messages.poor'), 'class' => 'bg-danger'],
                                                ];
                                            @endphp
                                            <div class="row align-items-center g-3">
                                                @foreach ($ratingLabels as $rating => $ratings)
                                                @php
                                                    // Calculate the count of each rating
                                                    $ratingCount = $data->review->where('rating', (string) $rating . '.0')->count('rating');

                                                    // Calculate the percentage for each rating
                                                    $percentage = $totalReviews > 0 ? ($ratingCounts[$rating]/ $totalReviews) * 100 : 0;
                                                @endphp
                                                    <div class="col-md-3 col-12">                                                    
                                                        <div class="d-flex align-items-center gap-1">
                                                                <span class="h6 mb-0">{{ $ratings['label'] }}</span>                                                       
                                                                <i class="ph-fill ph-star text-warning"></i>                                                    
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7 col-9">
                                                        <div class="progress w-100 progress-ratings" role="progressbar" aria-label="Basic example" aria-valuenow="{{ $ratingCounts[$rating] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar {{ $ratings['class'] }}" style="width: {{ $percentage ?? 0 }}%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-3 flex-shrink-0">
                                                        <span class="heading-color">{{ $ratingCounts[$rating] ?? 0 }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="review-table-container" class="mt-4">
                            <!-- Table will be dynamically inserted here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush
@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script src="{{ asset('js/apexcharts.js') }}"></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>
    <script src="{{ asset('js/form/index.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script type="text/javascript" defer>
        let currentTable = null;

        // Define columns for all tables
        const appointmentColumns = [
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
            {
                data: 'id',
                name: 'id',
                title: "{{ __('messages.id') }}"
            },
            {
                data: 'customer',
                name: 'customer',
                title: "{{ __('messages.lbl_customer') }}",
                orderable: true,
            },

            {
                data: 'collector',
                name: 'collector',
                title: "{{ __('messages.lbl_collector') }}",
                orderable: true,
            },
           
            {
                data: 'test_id',
                name: 'test_id',
                title: "{{ __('messages.lbl_test_case') }}"
            },
           
            {
                data: 'total_amount',
                name: 'total_amount',
                title: "{{ __('messages.lbl_total_amount') }}"
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                title: "{{ __('messages.lbl_payment_status') }}"
            },
            {
                data: 'status',
                name: 'status',
                title: "{{ __('messages.lbl_status') }}",
                width: '5%',
            },

            
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                title: "{{ __('messages.lbl_action') }}",
                width: '5%'
            }
        ]

        const reviewColumns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
                visible: false,
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
            {
                data: 'id',
                name: 'id',
                title: "{{ __('messages.id') }}"
            },
            // {
            //     data: 'collector',
            //     name: 'collector',
            //     title: "{{ __('messages.lbl_collector') }}"
            // },
            {
                data: 'user',
                name: 'user',
                title: "{{ __('messages.lbl_customer') }}",
            },
            {
                data: 'rating',
                name: 'rating',
                title: "{{__('messages.lbl_rating')}}"
            },
            {
                data: 'review',
                name: 'review',
                class: 'description-column',
                title: "{{__('messages.review')}}"
            },
            // {
            //     data: 'action',
            //     name: 'action',
            //     orderable: false,
            //     searchable: false,
            //     title: "{{ __('messages.lbl_action') }}",
            //     width: '5%'
            // }
        ];

        const paymentColumns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
                visible: false,
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
            {
                data: 'id',
                name: 'id',
                title: "{{ __('messages.id') }}"
            },
            {
                data: 'appointment_id',
                name: 'appointment_id',
                title: "{{__('messages.appointment_id')}}"
            },
            {
                data: 'total_amount',
                name: 'total_amount',
                title: "{{__('messages.lbl_amount')}}"
            },
            {
                data: 'payment_method',
                name: 'payment_method',
                title: "{{__('messages.lbl_payment_method')}}"
            },
            {
                data: 'status',
                name: 'status',
                title: "{{__('messages.lbl_status')}}"
            },
            {
                data: 'transaction_id',
                name: 'transaction_id',
                title: "{{__('messages.transaction_id')}}"
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                title: "{{ __('messages.lbl_action') }}",
                width: '5%'
            }
        ];
        $('#labTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            if (window.renderedDataTable) {
                window.renderedDataTable.destroy();
            }
            document.querySelector('.nav-link.active').classList.add('bg-secondary');
            // Listen for tab changes
            const tabs = document.querySelectorAll('.nav-link');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    // Remove bg-secondary from all tabs
                    tabs.forEach(t => t.classList.remove('bg-secondary'));
                    // Add bg-secondary to newly active tab
                    e.target.classList.add('bg-secondary');
                });
            });

            // Clear all table containers
            $('#appointment-table-container').empty();
            $('#payment-table-container').empty();
            $('#review-table-container').empty();

            if (e.target.id === 'appointments-tab') {
                $('#appointment-table-container').html(
                    '<table id="datatable" class="table table-responsive"></table>');

                initDatatable({
                    url: '{{ route('backend.appointments.index_data') }}',
                    finalColumns: appointmentColumns,
                    orderColumn: [
                        [1, "desc"]
                    ],
                    advanceFilter: () => {
                        return {
                            lab_id: '{{ $data->id }}'
                        }
                    }
                });
            } else if (e.target.id === 'payments-tab') {
                $('#payment-table-container').html('<table id="datatable" class="table table-responsive"></table>');

                initDatatable({
                    url: '{{ route('backend.appointments.index_data') }}',
                    finalColumns: paymentColumns,
                    orderColumn: [
                        [1, "desc"]
                    ],
                    advanceFilter: () => {
                        return {
                            lab_id: '{{ $data->id }}',
                            payment_status: 'paid',
                            type: 'payment'
                        }
                    }
                });
            } else if (e.target.id === 'reviews-tab') {
                $('#review-table-container').html('<table id="datatable" class="table table-responsive"></table>');

                initDatatable({
                    url: '{{ route('backend.reviews.index_data', ['lab_id' => $data->id]) }}',
                    finalColumns: reviewColumns,
                    orderColumn: [
                        [1, "desc"]
                    ],
                    advanceFilter: () => {
                        return {
                            lab_id: '{{ $data->id }}'
                        }
                    }
                });
            }
        });

        // Initialize the overview tab on page load
        document.addEventListener('DOMContentLoaded', (event) => {
            const activeTab = document.querySelector('#labTabs .nav-link.active');
            if (activeTab) {
                $(activeTab).trigger('shown.bs.tab');
            }
        });



        revanue_chart('Year')

        var chart = null;
        let revenueInstance;

        function revanue_chart(type) {
            var Base_url = "{{ url('/') }}";
            var lab_id = "{{ $data->id ?? null }}"; // Ensure this is correctly outputted by Blade
            var vendor_id = "{{ $data->vendor_id ?? null }}"; // Ensure this is correctly outputted by Blade
            var url = Base_url + "/app/get_revnue_chart_data/" + type;

            $("#revenue_loader").show();

            $.ajax({
                url: url,
                method: "GET",
                data: {
                    lab_id: lab_id,
                    vendor_id: vendor_id,
                }, // Corrected syntax here
                success: function(response) {
                    $("#revenue_loader").hide();
                    $(".total_revenue").text(type);

                    if (document.querySelectorAll('#chart-top-revenue').length) {
                        const monthlyTotals = response.data.chartData;
                        const category = response.data.category;

                        const options = {
                            series: [{
                                name: "Total Revenue",
                                data: monthlyTotals
                            }],
                            chart: {
                                height: 350,
                                type: 'line',
                                zoom: {
                                    enabled: false
                                }
                            },
                            colors: ['var(--bs-primary'],
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                            },
                            grid: {
                                borderColor: 'var(--bs-border-color)',
                                row: {
                                    colors: ['var(--bs-heading-color)', 'transparent'],
                                    opacity: 0
                                },
                            },
                            xaxis: {
                                labels: {
                                    style: {
                                        colors: "var(--bs-heading-color)"
                                    }
                                },
                                categories: category
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        colors: ['var(--bs-heading-color)'],
                                    },
                                     formatter: function(value) {
                                        return currencyFormat(value); // Format Y-axis labels as currency
                                     }
                                }
                            },
                            tooltip: {
                                style: {
                                    background: 'var(--bs-body-bg)',
                                    color: 'var(--bs-body-bg)'
                                },
                                y: {
                                    formatter: function(value) {
                                        return currencyFormat(value); // Currency formatting
                                    }
                                }
                            },
                        };

                        if (revenueInstance) {
                            revenueInstance.updateOptions(options);
                        } else {
                            revenueInstance = new ApexCharts(document.querySelector("#chart-top-revenue"),
                                options);
                            revenueInstance.render();
                        }
                    }
                }
            });
        }

        $(document).on('click', '.revenue-dropdown-item', function() {
            var type = $(this).data('type');
            revanue_chart(type);
        });
    </script>
@endpush
