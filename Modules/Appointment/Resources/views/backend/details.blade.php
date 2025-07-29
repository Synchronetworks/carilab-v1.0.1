@extends('backend.layouts.app')
@section('title')
    {{ __($module_title) }}
@endsection
@section('content')
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-md-center flex-wrap flex-md-row flex-column gap-3 mb-3">
            <div class="d-flex gap-3 flex-wrap">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                    data-bs-target="#uploadedReportModal" onclick="fetchReports({{ $data->customer_id }})">
                    <i class="ph ph-file-text"></i> {{ __('messages.uploaded_report') }}
                </button>
                <!-- Add the new status history button here -->
                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                    data-bs-target="#statusHistoryModal">
                    <i class="ph ph-clock"></i> {{ __('messages.status_history') }}
                </button>
                @if ($data->getFirstMedia('medical_report'))
                    <a href="{{ $data->getFirstMediaUrl('medical_report') }}" target="_blank"
                        class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-1">
                        <i class="ph ph-eye"></i>{{ __('messages.medical_report') }}
                    </a>
                @endif
                @if ($data->status == 'completed' && optional($data->transactions)->payment_status == 'paid')
                    <a href="{{ route('backend.appointments.invoice', $data->id) }}" class="btn btn-sm btn-secondary"
                        target="_blank">{{ __('messages.download_invoice') }}</a>
                @endif
            </div>
            <!-- Back Button -->
            <div class="text-end">
                <a href="{{ route('backend.appointments.index') }}" class="btn btn-sm btn-primary">
                    <i class="ph ph-caret-double-left"></i>{{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="row gy-4">
            <div class="col-md-8">
                <!-- Appointment Info -->
                <div class="card-input-title mb-3">
                    <h4 class="m-0">{{ __('messages.appointment_info') }}</h4>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- Appointment Basic Info -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <h5 class="mb-0 me-3">{{ __('messages.lbl_id') }} <span
                                    class="text-primary">#{{ $data->id }}</span></h5>
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <div class="">{{ __('messages.lbl_payment_status') }}:</div>
                                @php
                                    $payment_status = optional($data->transactions)->payment_status ?? 'Pending';
                                    if ($payment_status != 'Pending') {
                                        $payment_status = str_replace('_', ' ', $payment_status);
                                        $payment_status = ucwords($payment_status);
                                    }
                                    $status_class = match (strtolower($payment_status)) {
                                        'pending' => 'text-warning',
                                        'failed', 'declined' => 'text-danger',
                                        'paid', 'successful' => 'text-success',
                                        default => 'text-secondary',
                                    };
                                @endphp
                                <div class="{{ $status_class }}">
                                    {{ $payment_status }}
                                </div>

                            </div>
                        </div>

                        <div class="card bg-body">
                            <div class="card-body">
                                <!-- Date & Time -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div>
                                        <div class="mb-2">{{ __('messages.datetime') }}:</div>
                                        <h5 class="m-0">
                                            {{ $data->appointment_date ? App\Models\Setting::formatDate($data->appointment_date) : '-' }}
                                            <span class="text-body"> at </span>
                                            {{ $data->appointment_time ? App\Models\Setting::formatTime($data->appointment_time) : '-' }}
                                        </h5>
                                    </div>

                                    <!-- Lab Info -->
                                    <div>
                                        <div class="mb-2">{{ __('messages.lbl_lab_name') }}:</div>
                                        <h5 class="m-0">{{ optional($data->lab)->name ?? 'Unknown lab' }}</h5>
                                    </div>

                                    <!-- Service Info -->
                                    <div>
                                        <div class="mb-2">{{ __('messages.lbl_test_case') }}</div>
                                        <h5 class="m-0">
                                            {{ $data->test_type == 'test_case' ? optional($data->catlog)->name : optional($data->package)->name }}
                                        </h5>
                                    </div>

                                    <!-- Booking Status -->
                                    <div>
                                        <div class="mb-2">{{ __('messages.lbl_appointment_status') }}:</div>
                                        <h5 class="m-0 text-success d-flex align-items-center gap-2"> @php
                                            $status = $data->status ?? '-';
                                            if ($status != '-') {
                                                $status = str_replace('_', ' ', $status);
                                                $status = ucwords($status);
                                            }
                                        @endphp
                                            {{ $status }}
                                        </h5>
                                    </div>
                                    @if ($status === 'Completed')
                                        <div>
                                            <div class="mb-2">{{ __('messages.submission_status') }}:</div>
                                            <h5
                                                class="m-0 {{ $data->submission_status === 'submitted' ? 'text-success' : 'text-warning' }}">
                                                @php
                                                    $submissionStatus = $data->submission_status ?? 'pending';
                                                    $submissionStatus = str_replace('_', ' ', $submissionStatus);
                                                    $submissionStatus = ucwords($submissionStatus);
                                                @endphp
                                                {{ $submissionStatus }}
                                            </h5>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <!-- Sample Collection -->
                        <div class="mb-3">
                            <div class="">{{ __(key: 'messages.sample_collected') }}: <span
                                    class="heading-color fw-bold">
                                    {{ $data->collection_type ?? __('messages.labs') }} </span></div>
                        </div>
                        @if ($data->symptoms)
                            <div class="mb-3">
                                <div class="">{{ __('messages.symptoms') }}: <span class="heading-color fw-bold">
                                        {!! html_entity_decode($data->symptoms) ?? '-' !!}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Info Cards Row -->
                <div class="row gy-4">
                    <!-- Lab Info -->
                    <div class="col-lg-6">
                        <h6 class="mb-3">{{ __('messages.lbl_lab_info') }}</h6>
                        <div class="card mb-0">
                            <div class="card-body p-4">
                                @if ($data->lab && $data->lab !== null)
                                    <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap">
                                        <div class="rounded">
                                            <img src="{{ optional($data->lab)->logo_url ?? asset('img/default-lab.png') }}"
                                                alt="Lab Logo" class="avatar avatar-60 rounded-circle object-fit-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.labs.details', $data->lab->id) }}">
                                                <h5 class="mb-2">
                                                    {{ optional($data->lab)->name ?? __('messages.Unknown_Lab') }}</h5>
                                            </a>
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-1 text-break">
                                                    <i class="ph ph-envelope fs-4 heading-color"></i>
                                                    {{ optional($data->lab)->email ?? '-' }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ph ph-phone fs-4 heading-color"></i>
                                                    {{ optional($data->lab)->phone_number ?? '-' }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="mb-3">{{ __('messages.lab_deleted') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="col-lg-6">
                        <h6 class="mb-3">{{ __('messages.customer_info') }}</h6>
                        <div class="card mb-0">
                            <div class="card-body p-4">
                                @if ($data->customer && $data->customer !== null)
                                    <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap">
                                        <div class="rounded">
                                            <img src="{{ optional($data->customer)->profile_image ?? asset('img/default-avatar.png') }}"
                                                alt="Customer Avatar"
                                                class="avatar avatar-60 rounded-circle object-fit-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.users.details', $data->customer->id) }}">
                                                <h5 class="mb-2">
                                                    {{ optional($data->customer)->full_name ?? __('messages.Unknown_Customer') }}
                                                </h5>
                                            </a>
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-1 text-break">
                                                    <i class="ph ph-envelope fs-4 heading-color"></i>
                                                    {{ optional($data->customer)->email ?? '-' }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ph ph-phone fs-4 heading-color"></i>
                                                    {{ optional($data->customer)->mobile ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="mb-3">{{ __('messages.customer_deleted') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Other Member Info -->
                    @if ($data->othermember)
                        <div class="col-lg-6">
                            <h6 class="mb-3">{{ __('messages.other_member') }}</h6>
                            <div class="card mb-0">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap">
                                        <div class="rounded">
                                            <img src="{{ optional($data->othermember)->profile_image ?? asset('img/default-avatar.png') }}"
                                                alt="Member Avatar"
                                                class="avatar avatar-60 rounded-circle object-fit-cover">
                                        </div>
                                        <div>
                                            <h5 class="mb-2">
                                                {{ optional($data->othermember)->full_name ?? __('messages.Unknown_other_member') }}
                                            </h5>
                                            <div class="d-flex flex-wrap align-items-center gap-3">

                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ph ph-users fs-4 heading-color"></i>
                                                    {{ optional($data->othermember)->relation ?? '-' }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ph ph-phone fs-4 heading-color"></i>
                                                    {{ optional($data->othermember)->phone ?? '-' }}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    <!-- Vendor Info -->
                    <div class="col-lg-6">
                        <h6 class="mb-3">{{ __('messages.vendor_info') }}</h6>
                        <div class="card mb-0">
                            <div class="card-body p-4">
                                @if ($data->vendor && $data->vendor !== null)
                                    <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap">
                                        <div class="rounded">
                                            <img src="{{ optional($data->vendor)->profile_image ?? asset('img/default-avatar.png') }}"
                                                alt="Vendor Avatar"
                                                class="avatar avatar-60 rounded-circle object-fit-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.vendors.details', $data->vendor->id) }}">
                                                <h5 class="mb-2">{{ optional($data->vendor)->full_name ?? '-' }}</h5>
                                            </a>
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-1 text-break">
                                                    <i class="ph ph-envelope fs-4 heading-color"></i>
                                                    {{ optional($data->vendor)->email ?? '-' }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ph ph-phone fs-4 heading-color"></i>
                                                    {{ optional($data->vendor)->mobile ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="mb-3">{{ __('messages.vendor_deleted') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Collector Info -->
                    <div class="col-lg-6">
                        <h6 class="mb-3">{{ __('messages.collector_info') }}</h6>
                        <div class="card mb-0">
                            <div class="card-body">
                                @if ($data->appointmentCollectorMapping && $data->appointmentCollectorMapping->collector)
                                    <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap">
                                        <div class="rounded">
                                            <img src="{{ optional(optional($data->appointmentCollectorMapping)->collector)->profile_image ?? asset('img/default-avatar.png') }}"
                                                alt="Collector Avatar"
                                                class="avatar avatar-60 rounded-circle object-fit-cover">
                                        </div>
                                        <div>
                                            <a
                                                href="{{ route('backend.collectors.details', optional(optional($data->appointmentCollectorMapping)->collector)->id) }}">
                                                <h5 class="mb-2">
                                                    {{ optional(optional($data->appointmentCollectorMapping)->collector)->full_name ?? __('messages.Unknown_collector') }}
                                                </h5>
                                            </a>
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-1 text-break">
                                                    <i class="ph ph-envelope fs-4 heading-color"></i>
                                                    {{ optional(optional($data->appointmentCollectorMapping)->collector)->email ?? '-' }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ph ph-phone fs-4 heading-color"></i>
                                                    {{ optional(optional($data->appointmentCollectorMapping)->collector)->mobile ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="mb-0">{{ __('messages.no_collector') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if ($data->complaints)
                    <!-- Customer Complaints Section -->
                    <div class="mt-4">
                        <h6 class="mb-3">{{ __('messages.customer_complaints') }}</h6>
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-danger mb-2">{{ __('messages.issue_with_lab') }}</h6>
                                <p class="text-muted mb-0">{{ $data->complaints }}</p>
                                <div class="mt-2">
                                    <span class="badge bg-success-subtle">{{ __('messages.resolved') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Payment Details -->
            <div class="col-md-4">
                <h4 class="mb-3">{{ __('messages.payment_info') }}</h4>
                <div class="card">
                    <div class="card-body">
                        <!-- Price Breakdown -->
                        <div class="">
                            <div class="d-flex justify-content-between mb-3">
                                <div>{{ __('messages.lbl_amount') }}</div>
                                <h6 class="m-0">{{ \Currency::format($data->amount ?? 0) }}</h6>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div class="text-success">
                                    {{ __('messages.lbl_discount') }}
                                    ({{ optional($data->transactions)->discount_type == 'percentage'
                                        ? optional($data->transactions)->discount_value . '%'
                                        : Currency::format(optional($data->transactions)->discount_value ?? 0) }})
                                </div>

                                <h6 class="m-0">
                                    {{ \Currency::format(optional($data->transactions)->discount_amount ?? 0) }}
                                </h6>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                @php
                                    $coupon = optional($data->transactions)->coupon;
                                    $couponData = is_array($coupon) ? $coupon : [];
                                @endphp
                                @if (!empty($couponData))
                                    <div>
                                        {{ __('messages.coupon_discount') }} (
                                        {{ $couponData['coupon_code'] ?? '-' }}:
                                        {{ ($couponData['discount_type'] ?? null) == 'percentage'
                                            ? ($couponData['discount_value'] ?? '0') . '%'
                                            : \Currency::format($couponData['discount_value'] ?? 0) }}
                                        )
                                    </div>
                                    <h6 class="m-0">
                                        {{ \Currency::format(optional($data->transactions)->coupon_amount ?? 0) }}
                                    </h6>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div>{{ __('messages.subtotal') }}</div>
                                <h6 class="m-0">{{ \Currency::format($data->test_discount_amount ?? 0) }}</h6>
                            </div>
                            <div class="d-flex justify-content-between gap-2 flex-wrap mb-3">
                                <h6 class="m-0">{{ __('messages.lbl_taxes') }}:</h6>
                                <h6 class="m-0 text-danger">
                                    {{ \Currency::format(optional($data->transactions)->total_tax_amount ?? 0) }}
                                </h6>
                            </div>
                            @if (optional($data->transactions)->tax)
                                <div class="p-3 applied-tax rounded bg-body">
                                    <h6 class="mb-2">{{ __('messages.applied_tax') }}</h6>
                                    @foreach (json_decode(optional($data->transactions)->tax) as $tax)
                                        @php
                                            $taxAmount =
                                                $tax->type == 'Percentage'
                                                    ? ($data->test_discount_amount * $tax->value) / 100
                                                    : $tax->value;
                                        @endphp
                                        <div class="d-flex justify-content-between applied-tax-item">
                                            <div>{{ $tax->title }}
                                                ({{ $tax->type == 'Percentage' ? $tax->value . '%' : \Currency::format($tax->value) }})
                                            </div>
                                            <h6 class="m-0">{{ \Currency::format($taxAmount) }}</h6>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="d-flex justify-content-between border-top pt-3 mt-3">
                                <div class="fw-bold heading-color">{{ __('messages.grand_total') }}</div>
                                <h6 class="fw-bold text-primary">{{ \Currency::format($data->total_amount ?? 0) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Status History Modal -->
    <div class="modal fade" id="statusHistoryModal" tabindex="-1" aria-labelledby="statusHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="statusHistoryModalLabel">{{ __('messages.appointment_status') }}</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-4">
                        <!-- Stakeholder Info -->
                        <div class="col-md-6">

                            <!-- Lab Info -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2">{{ __('messages.labs') }}:</h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <img src="{{ optional($data->lab)->logo_url ?? asset('img/default-lab.png') }}"
                                        alt="Lab" class="avatar avatar-40 rounded-circle object-fit-cover">
                                    <span>{{ optional($data->lab)->name ?? __('messages.unknown_lab') }}</span>
                                </div>
                            </div>

                            <!-- Vendor Info -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2">{{ __('messages.lbl_vendor') }}:</h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <img src="{{ optional($data->vendor)->profile_image ?? asset('img/default-avatar.png') }}"
                                        alt="Vendor" class="avatar avatar-40 rounded-circle object-fit-cover">
                                    <span>{{ optional($data->vendor)->full_name ?? __('messages.no_vendor') }}</span>
                                </div>
                            </div>

                            <!-- Collector Info -->
                            <div>
                                <h6 class="fw-bold mb-2">{{ __('messages.collectors') }}:</h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if (optional(optional($data->appointmentCollectorMapping)->collector)->profile_image)
                                        <img src="{{ optional(optional($data->appointmentCollectorMapping)->collector)->profile_image ?? asset('img/default-avatar.png') }}"
                                            alt="Collector" class="avatar avatar-40 rounded-circle object-fit-cover">
                                    @endif
                                    <span>{{ optional(optional($data->appointmentCollectorMapping)->collector)->full_name ?? __('messages.no_collector') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Timeline -->
                        <div class="col-md-6">

                            <div class="timeline-steps">
                                <!-- Created Status -->
                                <div class="timeline-step completed">
                                    <div class="timeline-content">
                                        <div class="inner-circle"></div>
                                        <h6 class="mb-1">{{ __('messages.created_by_helpdesk') }}</h6>
                                        <p class="mb-0">
                                            {{ App\Models\Setting::formatDate($data->created_at) . ' ' . App\Models\Setting::formatTime($data->created_at) }}
                                        </p>
                                    </div>
                                </div>



                                <!-- Collector Assignment Status -->
                                @if ($data->appointmentCollectorMapping && $data->appointmentCollectorMapping->created_at)
                                    <div
                                        class="timeline-step {{ in_array($data->status, ['completed', 'submitted']) ? 'completed' : '' }}">
                                        <div class="timeline-content">
                                            <div class="inner-circle"></div>
                                            <h6 class="mb-1">{{ __('messages.collector_assigned') }}</h6>
                                            <p class="mb-0">
                                                {{ App\Models\Setting::formatDate(App\Models\Setting::timeZone($data->appointmentCollectorMapping->created_at)) . ' ' . App\Models\Setting::formatTime(App\Models\Setting::timeZone($data->appointmentCollectorMapping->created_at)) }}
                                            </p>
                                            <p class="small text-muted mt-2">
                                                {{ optional(optional($data->appointmentCollectorMapping)->collector)->full_name ?? __('messages.Unknown_collector') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Confirmed Status -->
                                @if ($data->status !== 'pending')
                                    <div
                                        class="timeline-step {{ in_array($data->status, ['confirmed', 'completed', 'submitted']) ? 'completed' : '' }}">
                                        <div class="timeline-content">
                                            <div class="inner-circle"></div>
                                            <h6 class="mb-1">
                                                @if ($data->status === 'completed')
                                                    {{ __('messages.appointment_completed') }}
                                                @else
                                                    {{ ucfirst($data->status) }}
                                                @endif
                                            </h6>
                                            <p class="mb-0">
                                                @php
                                                    $statusDate = match ($data->status) {
                                                        'confirmed' => $data->confirmed_at ?? $data->updated_at,
                                                        'completed' => $data->completed_at ?? $data->updated_at,
                                                        'submitted' => $data->submission_date ?? $data->updated_at,
                                                        default => $data->updated_at,
                                                    };
                                                @endphp
                                                {{ $statusDate ? App\Models\Setting::formatDate(App\Models\Setting::timeZone($statusDate)) . ' ' . App\Models\Setting::formatTime(App\Models\Setting::timeZone($statusDate)) : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Sample Collection Status -->
                                @if ($data->collection_date)
                                    <div
                                        class="timeline-step {{ in_array($data->status, ['completed', 'submitted']) ? 'completed' : '' }}">
                                        <div class="timeline-content">
                                            <div class="inner-circle"></div>
                                            <p class="h6 mt-3 mb-1">{{ __('messages.sample_collected') }}</p>
                                            <p class="h6 text-muted mb-0">
                                                {{ App\Models\Setting::formatDate(App\Models\Setting::timeZone($data->collection_date)) . ' ' . App\Models\Setting::formatTime(App\Models\Setting::timeZone($data->collection_date)) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif



                                <!-- Report Submission Status -->
                                @if ($data->submission_status === 'submitted')
                                    <div class="timeline-step completed">
                                        <div class="timeline-content">
                                            <div class="inner-circle"></div>
                                            <h6 class="mb-1">{{ __('messages.report_submitted') }}</h6>
                                            <p class="mb-0">
                                                {{ $data->submission_date ? \Carbon\Carbon::parse($data->submission_date)->format('d M Y, h:i A') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Cancelled Status -->
                                @if ($data->status === 'cancelled')
                                    <div class="timeline-step">
                                        <div class="timeline-content">
                                            <div class="inner-circle bg-danger"></div>
                                            <h6 class="mb-1 text-danger">{{ __('messages.cancelled') }}</h6>
                                            <p class="mb-0">
                                                {{ $data->cancelled_at ? \Carbon\Carbon::parse($data->cancelled_at)->format('d M Y, h:i A') : '-' }}
                                            </p>
                                            @if ($data->cancellation_reason)
                                                <p class="small mt-2">
                                                    {{ __('messages.reason') }}: {{ $data->cancellation_reason }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Payment Status Timeline -->
                                @if ($data->status === 'completed')
                                    <div class="timeline-step completed">
                                        <div class="timeline-content">
                                            <div class="inner-circle"></div>
                                            <h6 class="mb-1">{{ __('messages.payment_status') }}</h6>
                                            <p class="mb-0">
                                                {{ optional($data->transactions)->payment_status ? ucfirst(optional($data->transactions)->payment_status) : 'Pending' }}
                                            </p>
                                            @if (optional($data->transactions)->payment_method)
                                                <p class="small mt-2">
                                                    {{ __('messages.payment_method') }}:
                                                    {{ ucfirst(optional($data->transactions)->payment_method) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Report Progress Timeline -->
                                @if ($data->status === 'completed')
                                    <!-- Report Generation -->
                                    <div
                                        class="timeline-step {{ $data->test_case_status == 'report_generated' ? 'completed' : '' }}">
                                        <div class="timeline-content">
                                            <div class="inner-circle"></div>
                                            <h6 class="mb-1">{{ __('messages.report_generation') }}</h6>
                                            <p class="mb-0">
                                                {{ $data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->format('d M Y, h:i A') : '-' }}
                                            </p>
                                        </div>
                                    </div>



                                    <!-- Report Submission -->
                                    @if ($data->submission_status === 'submitted')
                                        <div class="timeline-step completed">
                                            <div class="timeline-content">
                                                <div class="inner-circle"></div>
                                                <h6 class="hmb-1">{{ __('messages.report_submitted') }}</h6>
                                                <p class="mb-0">
                                                    {{ $data->submission_date ? \Carbon\Carbon::parse($data->submission_date)->format('d M Y, h:i A') : '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.closed') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report List Modal -->
    <div class="modal fade" id="uploadedReportModal" tabindex="-1" aria-labelledby="uploadedReportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="uploadedReportModalLabel">{{ __('messages.customer_uploaded') }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('messages.report_name') }}</th>
                                <th>{{ __('messages.lbl_uploaded_at') }}</th>
                                <th>{{ __('messages.lbl_action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="reportList">
                            <tr>
                                <td colspan="3" class="text-center">{{ __('messages.no_reports') }}.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.closed') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('after-styles')
@endpush
<script>
    function fetchReports(userId) {
        $.ajax({
            url: '{{ url('api/report-list') }}', // Your backend route
            type: "GET",
            data: {
                user_id: userId
            },
            success: function(response) {
                let reports = response.data;
                let html = "";

                if (reports.length > 0) {
                    reports.forEach(function(report) {
                        let actionBtns = '';

                        if (Array.isArray(report.attachments) && report.attachments.length > 0) {
                            report.attachments.forEach(function(fileUrl) {
                                if (typeof fileUrl === 'string') {
                                    let fileType = fileUrl.split('.').pop().toLowerCase();

                                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                                        actionBtns +=
                                            `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-success me-2">View</a>`;
                                    } else if (fileType === 'pdf') {
                                        actionBtns +=
                                            `<a href="${fileUrl}" download class="btn btn-sm btn-primary me-2">Download</a>`;
                                    }
                                }
                            });
                        } else {
                            actionBtns = '<span class="text-muted">No attachments</span>';
                        }

                        html += `
                        <tr>
                            <td>${report.name}</td>
                            <td>${report.uploaded_at}</td>
                            <td>${actionBtns}</td>
                        </tr>
                    `;
                    });
                } else {
                    html =
                        `<tr><td colspan="3" class="text-center">{{ __('messages.no_reports') }}</td></tr>`;
                }

                $("#reportList").html(html);
            },
            error: function() {
                $("#reportList").html(
                    '<tr><td colspan="3" class="text-center text-danger">Failed to load reports.</td></tr>'
                    );
            }
        });
    }
</script>
