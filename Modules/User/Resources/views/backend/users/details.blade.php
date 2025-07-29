@extends('backend.layouts.app')
@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
<div class="page-content">
    <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
        <a href="{{ route('backend.customer.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div>
    <!-- Tabs -->
    <div class="d-flex align-items-center gap-2 mb-4">
       
        <div class="custom-tab-slider">
            <ul class="nav nav-pills" id="customerTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview">{{ __('messages.overview') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="appointments-tab" data-bs-toggle="tab" href="#appointments">{{ __('messages.appointments') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="payments-tab" data-bs-toggle="tab" href="#payments">{{ __('messages.payment_history') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="ratings-tab" data-bs-toggle="tab" href="#ratings">{{ __('messages.ratings_reviews') }}</a>
                </li>
            </ul>
        </div>

    </div>
    <!-- Tab Content -->
    <div class="tab-content" id="customerTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview">
            <div class="card">
                <div class="card-body">               
                    <!-- Appointment Statistics Cards -->
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <h3 class="text-primary">{{ $totalAppointments ?? 0 }}</h3>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-calendar-dots"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{ __('messages.lbl_total_appointments') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <h3 class="card-text text-primary">{{ $cancelledAppointments ?? 0 }}</h3>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-calendar-x"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{ __('messages.cancelled_appointments') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <h3 class="card-text  text-primary">{{ $completedAppointments ?? 0 }}</h3>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-calendar-check"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{ __('messages.completed_appointments') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <h3 class="card-text  text-primary">{{ $upcomingAppointments ?? 0 }}</h3>
                                        <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                            <i class="ph ph-calendar-plus"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-0">{{ __('messages.upcoming_appointments') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Basic Info Card -->
                    <h5 class="card-title mb-2">
                        @if($data->user_type === 'collector')
                        {{ __('messages.lbl_collector') }}     {{ __('messages.basic_information') }} 
                        @elseif($data->user_type === 'vendor')
                        {{ __('messages.lbl_vendor') }}   {{ __('messages.basic_information') }} 
                        @else
                        {{ __('messages.customer') }}   {{ __('messages.basic_information') }} 
                        @endif
                    </h5>             
                        <div class="card bg-body mb-0">
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-3">
                                    <!-- Profile Image Column -->
                                    <div class="avatar-wrapper">
                                        <img src="{{ isset($data->profile_image) && $data->profile_image ? setBaseUrlWithFileName($data->profile_image) : '#' }}" 
                                            alt="{{ $data->first_name }}" class="avatar avatar-60 rounded-circle">
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
                                                
                                            <div class="d-flex align-items-center gap-1">
                                            <i class="ph ph-map-pin"></i>
                                                <span>{{ $data->address ?? '--' }}</span>
                                            </div>
                                        </div>                                                            
                                    </div>
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
                    <div id="appointment-table-container">
                        <!-- Table will be dynamically inserted here -->
                    </div>
                </div>
            </div>            
        </div>

        <!-- Payment History Tab -->
        <div class="tab-pane fade" id="payments">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="bg-primary-subtle py-3 px-3 px-lg-5 rounded d-inline-flex justify-content-between align-items-center gap-3 gap-lg-5 mb-3">
                        <h6 class="heading-color mb-0">{{ __('messages.total_amount_paid') }}</h6>  
                        <h3 class="mb-0 text-primary">{{ \Currency::format($totalPaidAmount) ?? 0 }}</h3>
                    </div>
                    <div id="payment-table-container">
                        <div class="card bg-body mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-2">{{ __('messages.total_amount_paid') }}</h6>
                                    
                                    </div>
                                    <h3 class="mb-0 text-primary">₹{{ $totalPaidAmount ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div id="payment-table-container">
                            <!-- Table will be dynamically inserted here -->
                        </div>
                    </div>
                </div>
            </div>            
        </div>

        <!-- Ratings & Reviews Tab -->
        <div class="tab-pane fade" id="ratings">
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
                                                    5 => ['label' =>  __('messages.excellent') , 'class' => 'bg-success'],
                                                    4 => ['label' =>  __('messages.good') , 'class' => 'bg-primary'],
                                                    3 => ['label' =>  __('messages.average') , 'class' => 'bg-info'],
                                                    2 => ['label' =>  __('messages.below_average') , 'class' => 'bg-warning'],
                                                    1 => ['label' =>  __('messages.poor') , 'class' => 'bg-danger'],
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
                                                        <div class="d-flex align-items-center gap-2">
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
                        <div id="review-table-container">
                        <!-- Table will be dynamically inserted here -->
                        </div>
                    </div>
                </div>                           
                    
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push ('after-styles')
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
            @if($data->user_type !== 'user')
            {
                data: 'customer',
                name: 'customer',
                title: "{{ __('messages.lbl_customer') }}",
                orderable: true,
                render: function(data, type, row) {
                    if (!data) return '--';
                    return `<span class="customer-name">${data.first_name} ${data.last_name}</span>`;
                }
            },
            @endif

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
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                title: "{{ __('messages.lbl_action') }}",
                width: '5%'
            }
            

        ]

        const reviewColumns = [
         
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
                data: 'collector',
                name: 'collector',
                title: "{{__('messages.lbl_collector_name')}}"
            },
            {
                data: 'lab',
                name: 'lab',
                title: "{{__('messages.labs')}}",  // Assuming `name` is a field in the `Collector` model
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
                title: "{{ __('messages.lbl_review') }}"
            },

        ];

        const paymentColumns = [
        
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

        // Handle tab switching
        $('#customerTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
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

            if (e.target.id === 'appointments-tab') {
                $('#appointment-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.appointments.index_data", ["user_id" => $data->id]) }}',
                    finalColumns: appointmentColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            name: $('#user_name').val(),
                            user_id: '{{ $data->id }}'
                        }
                    }
                });
            } else if (e.target.id === 'ratings-tab') {
                $('#review-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.reviews.index_data", ["user_id" => $data->id]) }}',
                    finalColumns: reviewColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            name: $('#user_name').val(),
                            user_id: '{{ $data->id }}'
                        }
                    }
                });
            } else if (e.target.id === 'payments-tab') {
                $('#payment-table-container').html('<table id="datatable" class="table table-responsive"></table>');
                
                initDatatable({
                    url: '{{ route("backend.appointments.index_data", ["user_id" => $data->id]) }}',
                    finalColumns: paymentColumns,
                    orderColumn: [[1, "desc"]],
                    advanceFilter: () => {
                        return {
                            name: $('#user_name').val(),
                            user_id: '{{ $data->id }}',
                            payment_status: 'paid',
                        }
                    }
                });
            }
        });

        // Initialize table based on active tab
        document.addEventListener('DOMContentLoaded', (event) => {
            const activeTab = document.querySelector('#customerTabs .nav-link.active');
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
