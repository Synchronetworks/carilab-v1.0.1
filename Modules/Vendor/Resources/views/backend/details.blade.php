@extends('backend.layouts.app')

@section('title') 
    {{ __($module_title) }} 
@endsection

@section('content')
<div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
    <a href="{{ (auth()->user()->user_type === 'admin') ? route('backend.vendors.index') : route('backend.home') }}" class="btn btn-sm btn-primary">
        <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
    </a>
</div>
<div class="page-content">
    <!-- Tabs -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <div class="left" style="display: block;">
            <i class="ph ph-caret-left align-middle"></i>
        </div>
        <div class="custom-tab-slider">
            <ul class="nav nav-pills" id="collectorTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab">{{ __('messages.overview') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="collectors-tab" data-bs-toggle="tab" href="#collectorTab" role="tab">{{ __('messages.collectors') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="appointments-tab" data-bs-toggle="tab" href="#appointments" role="tab">{{ __('messages.appointments') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="payments-tab" data-bs-toggle="tab" href="#payments" role="tab">{{ __('messages.payment_history') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab">{{ __('messages.ratings_reviews') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="bank-details-tab" data-bs-toggle="tab" href="#bank-details" role="tab">{{ __('messages.bank_detail') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="lab-tab" data-bs-toggle="tab" href="#lab" role="tab">{{ __('messages.labs') }}</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="coupons-tab" data-bs-toggle="tab" href="#coupons" role="tab">{{ __('messages.coupons') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="payouts-vendor-tab" data-bs-toggle="tab" href="#payouts1" role="tab">{{ __('messages.payouts') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab" data-bs-toggle="tab" href="#documents" role="tab">{{ __('messages.documents') }}</a>
                </li>
            </ul>
        </div>
        <div class="right" style="display: block;">
            <i class="ph ph-caret-right align-middle"></i>
        </div>
    </div>
   

    <!-- Tab Content -->
    <div class="tab-content" id="collectorTabs">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @include('vendor::backend.VendorDetails.overview')
                </div>
            </div>
        </div>

        <!-- Appointments Tab -->
        <div class="tab-pane fade" id="appointments" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div id="appointment-table-container">
                        <!-- Table will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Tab -->
        <div class="tab-pane fade" id="payments" role="tabpanel">
            <!-- Payment Statistics Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-inline-flex justify-content-between align-items-center bg-primary-subtle rounded py-3 px-3 px-lg-5 mb-3 gap-3 gap-lg-5">
                        <h6 class="m-0 heading-color">{{ __('messages.total_revenue') }}</h6>
                        <h3 class="mb-0 text-primary">{{ \Currency::format($statistics['paidPayouts'] ?? 0 )}}</h3>
                    </div>
                    <div id="payment-table-container">
                        <!-- Table will be dynamically inserted here -->
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane fade" id="reviews" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <!-- Rating Statistics Cards -->
                    <div class="row gy-4 mb-3">
                            <div class="col-xl-3 col-md-6">
                                <div class="card flex-grow-1 bg-body h-100">
                                    <div class="card-body ">
                                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                            <p class="mb-3">{{ __('messages.total_reviews') }}</p>                                              
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
                                            <p class="mb-3">{{ __('messages.average_rating') }}:</p>
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
                                                $ratingCount = $data->reviews->where('rating', (string) $rating . '.0')->count('rating');

                                                // Calculate the percentage for each rating
                                                $percentage = $totalReviews > 0 ? ($ratingCounts[$rating]/ $totalReviews) * 100 : 0;
                                            @endphp
                                                <div class="col-md-3 col-12">                                                    
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="h6 mb-0 rating-text">{{ $ratings['label'] }}</span>                                                       
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
                    <div id="review-table-container">
                        <!-- Table will be dynamically inserted here -->
                    </div>
                </div>
            </div>                    
        </div>

        <!-- Bank Details Tab -->
        <div class="tab-pane fade" id="bank-details" role="tabpanel">
            <div class="card">
            
                <div class="card-body">
               @if(auth()->user()->hasRole('vendor'))
                <div class="d-flex justify-content-end gap-1 align-items-center mb-2">
                    <a href="{{ route('backend.vendor_bank.create') }}" class="btn btn-primary d-flex justify-content-center align-items-center gap-1">
                        <i class="ph ph-plus-circle align-middle"></i> {{ __('messages.new') }}</a>
                </div>
                @endif
                    <div id="bank-details-table-container"></div>
                </div>
            </div>
        </div>

        <!-- Payouts Tab -->
        <div class="tab-pane fade" id="payouts1" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div id="payouts-vendor-table-container"></div>
                </div>
            </div>
        </div>

        <!-- Documents Tab -->
        <div class="tab-pane fade" id="documents" role="tabpanel">
            <div class="card">
                <div class="card-body">
                @if(auth()->user()->hasRole('vendor'))
                <div class="d-flex justify-content-end gap-1 align-items-center mb-2">
                    <a href="{{ route('backend.vendordocument.create',['vendordocument' => $data->id??' ']) }}" class="btn btn-primary d-flex justify-content-center align-items-center gap-1">
                        <i class="ph ph-plus-circle align-middle"></i> {{ __('messages.new') }}</a>
                </div>
                @endif
               
                    <div id="documents-table-container"></div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="collectorTab" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div id="collector-table-container"></div>
                </div>
            </div>
            <!-- Collectors content -->
        </div>
        <div class="tab-pane fade" id="lab" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <h3 class="card-text text-primary">{{ \Currency::format($statistics['paidPayouts'] ?? 0 ) }}</h3>
                                        </div>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-currency-circle-dollar"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{__('messages.lbl_total_revenue')}} </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <h3 class="card-text text-primary">{{  $statistics['total_lab'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-test-tube"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{ __('messages.lbl_total_laboratories') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <h3 class="card-text text-primary">{{ $statistics['total_test'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-notebook"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{ __('messages.total_test_case') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                        <h3 class="card-text text-primary">{{ \Currency::format($statistics['pendingPayouts'] ?? 0)}}</h3>
                                        </div>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-hand-coins"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{ __('messages.lbl_total_pending_payout') }}</h6>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div id="lab-table-container"></div>
                    <!-- Lab content -->
                </div>
            </div>
            
        </div>
        <div class="tab-pane fade" id="disputes" role="tabpanel">
                <div class="card-body">
                    <div id="disputes-table-container"></div>
                </div>
        </div>
        <div class="tab-pane fade" id="coupons" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div id="coupons-table-container"></div>
                </div>
            </div>
            <!-- Coupons content -->
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
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>
    <script src="{{ asset('js/form/index.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script type="text/javascript" defer>
        let currentTable = null;

        // Define columns for all tables
        const appointmentColumns = [
            {
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
                title: "{{ __('messages.lbl_id') }}"
            },
            {
                data: 'customer',
                name: 'customer',
                title: "{{ __('messages.lbl_customer') }}",
                orderable:true,
            },
            {
                data: 'lab',
                name: 'lab',
                title: "{{ __('messages.lbl_lab') }}",
                orderable:true,
            },
            {
                data: 'collector',
                name: 'collector',
                title: "{{ __('messages.lbl_collector') }}",
                orderable:true,
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
            
            

        ]

        const reviewColumns = [
            {
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
                title: "{{ __('messages.lbl_id') }}"
            },
          
            {
                data: 'user',
                name: 'user',
                title: "{{ __('messages.lbl_customer') }}"
            },
            {
                data: 'rating',
                name: 'rating',
                title: "{{ __('messages.lbl_rating') }}"
            },
            {
                data: 'review',
                name: 'review',
                class: 'description-column',
                title: "{{ __('messages.reviews') }}"
            },
          
        ];

        const paymentColumns = [
            {
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
                title: "{{ __('messages.lbl_id') }}"
            },
            {
                data: 'appointment_id',
                name: 'appointment_id',
                title: "{{ __('messages.appointment_id') }}"
            },
            {
                data: 'total_amount',
                name: 'total_amount',
                title: "{{ __('messages.lbl_amount') }}"
            },
            {
                data: 'payment_method',
                name: 'payment_method',
                title: "{{ __('messages.lbl_payment_method') }}"
            },
            {
                data: 'status',
                name: 'status',
                title: "{{ __('messages.lbl_status') }}"
            },
            {
                data: 'transaction_id',
                name: 'transaction_id',
                title: "{{ __('messages.transaction_id') }}"
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

        const bankDetailsColumns = [
            {
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
                data: 'id',
                name: 'id',
                title: "{{ __('messages.lbl_id') }}"
            },
            {
                data: 'user_name',
                name: 'user_name',
                title: "{{ __('messages.account_holder') }}"
            },
            {
                data: 'account_no',
                name: 'account_no',
                title: "{{ __('messages.lbl_account_no') }}"
            },
            {
                data: 'bank_name',
                name: 'bank_name',
                title: "{{ __('messages.lbl_bank_name') }}"
            },
            {
                data: 'ifsc_code',
                name: 'ifsc_code',
                title: "{{ __('messages.lbl_ifsc_code') }}"
            },
            {
                data: 'status',
                name: 'status',
                title: "{{ __('messages.lbl_status') }}"
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

        const payoutsColumns = [
            {
               name: 'check',
               data: 'check',
               title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="vendorpayouts" onclick="selectAllTable(this)">',
               width: '0%',
               exportable: false,
               orderable: false,
               searchable: false,
               visible: false,
            },
            {
                data: 'payment_method',
                name: 'payment_method',
                title: "{{ __('messages.lbl_payment_method') }}"
            },
            {
                data: 'amount',
                name: 'amount',
                title: "{{ __('messages.lbl_amount') }}"
            },
            {
                data: 'paid_date',
                name: 'paid_date',
                title: "{{ __('messages.lbl_paid_date') }}"
            },
            {
              data: 'updated_at',
              name: 'updated_at',
              title: "{{ __('messages.lbl_update_at') }}",
              orderable: true,
             visible: false,
           },
        ];

        const documentsColumns =[
         {
           name: 'check',
           data: 'check',
           title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="collectordocument" onclick="selectAllTable(this)">',
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
            data: 'document_id',
            name: 'document_id',
            title: "{{ __('messages.document') }}"
          },
          {
            data: 'is_verified',
            name: 'is_verified',
            title: "{{ __('messages.is_verified') }}"
          },
          {
            data: 'status',
            name: 'status',
            title: "{{ __('messages.status') }}"
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.action') }}",
            className: 'text-end'
          }

        ];

        const collectorColmuns = [
           {
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
                data: 'name',
                name: 'name',
                title: "{{ __('messages.lbl_name') }}",
            },
            {
                data: 'lab_name',
                name: 'lab_name',
                title: "{{ __('messages.lbl_lab') }}",
            },
          
            ...(@json(multivendor()) == 1 && @json(auth()->user()->user_type) != 'vendor' ? [{
                data: 'vendor_name',
                name: 'vendor_name',
                title: "{{ __('messages.lbl_vendor') }}"
            }] : []),
            {
                data: 'contact_number',
                name: 'contact_number',
                title: "{{ __('messages.contact_number') }}",
                orderable: false,
                searchable: false,
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
            title: "{{ __('messages.action') }}",
            className: 'text-end'
          }
       ]

       const labColumns = [{
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
                data: 'name',
                name: 'name',
                title: "{{ __('messages.lbl_name') }}",
            },
          
           
            {
                data: 'test_case_counter',
                name: 'test_case_counter',
                title: "{{ __('messages.test_case_counter') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'booking_count',
                name: 'bookong_count',
                title: "{{__('messages.bookings')}}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'collectors_count',
                name: 'collectors_count',
                title: "{{__('messages.collectors')}}",
                orderable: false,
                searchable: false,
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
            title: "{{ __('messages.action') }}",
            className: 'text-end'
          }
        ]

    const couponColumns = [
            {
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
                data: 'coupon_code',
                name: 'coupon_code',
                title: "{{ __('messages.lbl_coupon_code') }}", // Coupon Code column
            },
            {
                data: 'lab',
                name: 'lab',
                title: "{{ __('messages.lbl_lab') }}", // Lab column
            },
            {
                data: 'discount_type',
                name: 'discount_type',
                title: "{{ __('messages.lbl_discount_type') }}", // Discount Type column
            },
            {
                data: 'discount_value',
                name: 'discount_value',
                title: "{{ __('messages.lbl_discount_value') }}", // Discount Value column
            },
            {
                data: 'per_customer_usage_limit',
                name: 'per_customer_usage_limit',
                title: "{{ __('messages.lbl_per_customer_usage_limit') }}", // Discount Type column
            },
            {
                data: 'total_usage_limit',
                name: 'total_usage_limit',
                title: "{{ __('messages.lbl_total_usage_limit') }}", // Discount Value column
            },
            {
                data: 'start_at',
                name: 'start_at',
                title: "{{ __('messages.lbl_start_at') }}", // Start Date column
            },
            {
                data: 'end_at',
                name: 'end_at',
                title: "{{ __('messages.lbl_end_at') }}", // End Date column
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
            title: "{{ __('messages.action') }}",
            className: 'text-end'
          }

        ]
        const disputesColumns = [

            {
                data: 'id',
                name: 'id',
                title: "{{ __('messages.lbl_id') }}"
            },
            {
                data: 'subject',
                name: 'subject',
                title: "{{ __('messages.lbl_subject') }}"
            },
            {
                data: 'created_at',
                name: 'created_at',
                title: "{{ __('messages.lbl_date_time') }}",
                render: function(data) {
                    return moment(data).format('DD-MM-YYYY HH:mm:ss');
                }
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
                title: "{{ __('messages.action') }}",
                className: 'text-end'
            }

        ]
        
        
            // Handle tab switching
        $('#collectorTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            // First destroy existing DataTable if it exists
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
            // Remove any existing datatable from all containers
            $('#appointment-table-container').empty();
            $('#review-table-container').empty();
            $('#payment-table-container').empty();
            $('#bank-details-table-container').empty();
            $('#payouts-vendor-table-container').empty();
            $('#documents-table-container').empty();
            $('#lab-table-container').empty();
            $('#coupons-table-container').empty();
            $('#disputes-table-container').empty();
            $('#collector-table-container').empty();
            if (e.target.id === 'appointments-tab') {
                $('#appointment-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.appointments.index_data", ["vendor_id" => $data->id]) }}',
                    finalColumns: appointmentColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            name: $('#user_name').val(),
                            user_id: '{{ $data->id }}'
                        }
                    }
                });
            } else if (e.target.id === 'reviews-tab') {
                $('#review-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.reviews.index_data", ["vendor_id" => $data->id]) }}',
                    finalColumns: reviewColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            name: $('#user_name').val(),
                            vendor_id: '{{ $data->id }}'
                        }
                    }
                });
            } else if (e.target.id === 'payments-tab') {
                $('#payment-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.appointments.index_data", ["vendor_id" => $data->id]) }}',
                    finalColumns: paymentColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            name: $('#user_name').val(),
                            vendor_id: '{{ $data->id }}',
                            payment_status: 'paid',
                        }
                    }
                });
            } else if (e.target.id === 'bank-details-tab') {
                $('#bank-details-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.banks.index_data") }}',
                    finalColumns: bankDetailsColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            user_id: '{{ $data->id }}'
                        }
                    }
                });
            } else if (e.target.id === 'payouts-vendor-tab') {
                $('#payouts-vendor-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                initDatatable({
                    url: '{{ route("backend.payouts.vendor_index_data", ["vendor_id" => $data->id]) }}',
                    finalColumns: payoutsColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                        }
                    }
                });
            } else if (e.target.id === 'documents-tab') {
                $('#documents-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.vendordocument.index_data", ["vendor_id" => $data->id]) }}',
                    finalColumns: documentsColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            vendor_id: '{{ $data->id }}'
                        }
                    }
                });
            
        } else if (e.target.id === 'collectors-tab') {
                $('#collector-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.collectors.index_data", ["vendor_id" => $data->id]) }}',
                    finalColumns: collectorColmuns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            vendor_id: '{{ $data->id }}'
                        }
                    }
                });
          
        } else if (e.target.id === 'lab-tab') {
                $('#lab-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.labs.index_data", ["vendor" => $data->id]) }}',
                    finalColumns: labColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            vendor_id: '{{ $data->id }}'
                        }
                    }
                });
            }
            else if (e.target.id === 'disputes-tab') {
                $('#disputes-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.helpdesks.index_data")}}',
                    finalColumns: disputesColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            vendor_id: '{{ $data->id }}'
                        }
                    }
                });
            }
            else if (e.target.id === 'coupons-tab') {
                $('#coupons-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.coupons.index_data", ["vendor_id" => $data->id]) }}',
                    finalColumns: couponColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            vendor_id: '{{ $data->id }}'
                        }
                    }
                });
            }
        });

        // Initialize table based on active tab
        document.addEventListener('DOMContentLoaded', (event) => {
            const activeTab = document.querySelector('#collectorTabs .nav-link.active');
            if (activeTab) {
                $(activeTab).trigger('shown.bs.tab');
            }
        });

        // Handle filter reset
        $('#reset-filter').on('click', function(e) {
            $('#user_name').val('');
            if (window.renderedDataTable) {
                window.renderedDataTable.ajax.reload(null, false);
            }
        });

        // Quick action functionality
        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');
                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction();
        });
    </script>
@endpush
