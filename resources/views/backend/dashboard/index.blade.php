@extends('backend.layouts.app', ['isBanner' => false])

@section('title') {{ __('messages.dashboard') }} @endsection

@section('content')
<script src="{{ asset('js/form/index.js') }}" defer></script>
<div class="container-fluid">
    <div class="row mt-3 ">

        <div class="col-12 mb-3">
            <div class="d-flex justify-content-end">
                @if(auth()->user()->hasRole('vendor'))
                <!-- Subscription Plan Section -->
                    <div class="col-md-8 upgrade-plan d-flex flex-wrap gap-3 align-items-center justify-content-between rounded p-4 bg-warning-subtle border border-warning">
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
                    <div class="col-md-4 d-flex justify-content-end gap-1 align-items-center">
                        <form action="{{ route('backend.home') }}" class="d-flex align-items-center gap-2 flex-end flex-wrap">
                            <div class="form-group my-0 flex-grow-1">
                                <input type="text" 
                                    name="date_range" 
                                    value="{{ $date_range }}" 
                                    class="form-control dashboard-date-range"
                                    
                                    readonly="readonly">
                            </div>
                            <button type="submit" 
                                    name="action" 
                                    value="filter" 
                                    class="btn btn-primary" 
                                    data-bs-toggle="tooltip"
                                    data-bs-position="bottom"
                                    data-bs-title="{{ __('messages.submit_date_filter') }}">
                                {{ __('messages.submit') }}
                            </button>
                        </form>
                    </div>
                    
                </div>
            </div>
        <div class="col-lg-8">

            <div class="row">
                @if (multivendor() == 1 && auth()->user()->user_type != 'vendor')
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('backend.vendors.index') }}">
                        <div class="card card-stats">
                            <div class="card-body">
                                <p class="mb-5 fs-6">{{ __('messages.lbl_total_active_vendors') }}</p>
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="card-data">
                                        <h2 class="mb-0 fs-2">{{ $activeVendors ?? 0 }}</h2>                                    
                                    </div>
                                    <div class="card-icon display-6 text-primary">
                                        <i class="ph ph-users-three"></i>
                                    </div> 
                                </div>                               
                            </div>
                        </div>
                    </a>
                </div>
            @elseif (multivendor() == 1 && auth()->user()->user_type == 'vendor')
                <div class="col-md-4 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <p class="mb-5 fs-6">{{ __('messages.lbl_total_pending_payout') }}</p>
                            <div class="d-flex align-items-center justify-content-between gap-3">                                
                                <div class="card-data">
                                    <h2 class="mb-0 fs-2">{{ Currency::format($totalpendingpayout) ?? 0 }}</h2>                                    
                                </div>
                                <div class="card-icon display-6 text-primary">
                                    <i class="ph ph-hand-coins"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @php
            $colSize = (multivendor() == 1) ? 'col-md-4' : 'col-md-4 col-lg-6'; 
        @endphp
                <!-- Total Active Collectors -->
                <div class="{{ $colSize }} col-sm-6">
                    <a href="{{ route('backend.collectors.index') }}">
                        <div class="card card-stats">
                            <div class="card-body">
                                <p class="mb-5 fs-6">{{ __('messages.lbl_total_active_collectors') }}</p>
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="card-data">
                                        <h2 class="mb-0 fs-2">{{ $activeCollectors ?? 0 }}</h2>                                        
                                    </div>  
                                    <div class="card-icon display-6 text-primary">
                                        <i class="ph ph-users"></i>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Laboratories -->
                <div class="{{ $colSize }} col-sm-6">
                    <a href="{{ route('backend.labs.index') }}">
                        <div class="card card-stats">
                            <div class="card-body">
                                <p class="mb-5 fs-6">{{ __('messages.lbl_total_laboratories') }}</p>
                                <div class="d-flex align-items-center justify-content-between gap-3">                                    
                                    <div class="card-data">
                                        <h2 class="mb-0 fs-2">{{ $totalLabs ?? 0 }}</h2>                                    
                                    </div>
                                    <div class="card-icon display-6 text-primary">
                                        <i class="ph ph-flask"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Active Customers -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('backend.customer.index') }}">
                        <div class="card card-stats">
                            <div class="card-body">
                                <p class="mb-5 fs-6">{{ auth()->user()->user_type == 'vendor' ? __('messages.lbl_total_customers') : __('messages.lbl_total_active_customers') }}</p>
                                <div class="d-flex align-items-center justify-content-between gap-3">  
                                    <div class="card-data">
                                        <h2 class="mb-0 fs-2">{{auth()->user()->user_type == 'vendor' ? ($vendorActiveCustomers ?? 0) : ($activeCustomers ?? 0);}}</h2>
                                    </div> 
                                    <div class="card-icon display-6 text-primary">
                                        <i class="ph ph-user-circle"></i>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Appointments -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('backend.appointments.index') }}">
                        <div class="card card-stats">
                            <div class="card-body">
                                <p class="mb-5 fs-6">{{ __('messages.lbl_total_appointments') }}</p>
                                <div class="d-flex align-items-center justify-content-between gap-3">  
                                    <div class="card-data">
                                        <h2 class="mb-0 fs-2">{{ $totalAppointments ?? 0 }}</h2>                                        
                                    </div>
                                    <div class="card-icon display-6 text-primary">
                                        <i class="ph ph-calendar-check"></i>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

              
                <div class="col-md-4 col-sm-6">
                  
                        <div class="card card-stats">
                            <div class="card-body">
                                <p class="mb-5 fs-6">{{ __('messages.lbl_total_revenue') }}</p>
                                <div class="d-flex align-items-center justify-content-between gap-3">  
                                    <div class="card-data">
                                        <h2 class="mb-0 fs-2">{{ Currency::format($total_revenue) }}</h2>
                                       
                                    </div>
                                    <div class="card-icon display-6 text-primary">
                                        <i class="ph ph-currency-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                   
                </div>
            </div>
        </div>


        <div class="col-lg-4">
            <div class="card card-stats">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.proportion_of_appointments') }}</h3>
                </div>
                <div class="card-body">
                    <div id="appointments-chart-tests-packages"></div>
                </div>
            </div>
        </div>

       


        <div class="col-md-12">
            <div class="card card-stats card-block card-height">
              
                <div class="card-body">
                    <div id="monthly-revenue-chart"></div>
                </div>
            </div>
        </div>
        @if(multivendor() == 1 && auth()->user()->user_type != 'vendor')
          
            <div class="col-lg-6 col-md-6">
                <div class="card card-block card-height">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <h3 class="card-title">{{ __('messages.soon_to_expire_vendors') }}</h3>
                        @if($soonToExpireVendors->count() > 5)
                            <a
                                href="{{ route('backend.users.soon-to-exipre', ['type' => 'soon-to-expire']) }}">{{ __('messages.view_all') }}</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-primary">
                                    <th>{{ __('messages.name') }}</th>
                                  
                                    <th>{{ __('messages.subscription_expiry') }}</th>
                                    <th>{{ __('messages.action') }}</th>
                                </thead>
                                <tbody>
                                    @foreach($soonToExpireVendors as $vendor)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex gap-3 align-items-center">
                                                                        <img src="{{ isset($vendor->profile_image) && $vendor->profile_image ? setBaseUrlWithFileName($vendor->profile_image) : '#' }}"
                                                                            alt="avatar" class="avatar avatar-40 rounded-pill">
                                                                        <div class="text-start">
                                                                            <h6 class="m-3">
                                                                                <a href="{{ route('backend.vendors.details', $vendor->id) }}"
                                                                                    class="text-decoration-none">
                                                                               
                                                                                  
                                                                                    <div class="text-start">    
                                                                                        <h6 class="m-0">{{ $vendor->full_name ?? default_user_name() }}</h6>
                                                                                        <span >{{ $vendor->email ?? '--' }}</span>
                                                                                      </div>
                                                                                </a>
                                                                            </h6>
                                                                        </div>
                                                                    </div>                                                    
                                                                </td>
                                                                <td>
                                                                    {{ optional($vendor->subscriptionPackage)->end_date
                                                                        ?  \App\Models\Setting::formatDate($vendor->subscriptionPackage->end_date)
                                                                        : '--' 
                                                                                                                        }}
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:void(0);" class="text-warning send-email-btn" data-user-id="{{ $vendor->id }}">
                                                                        <i class="ph ph-hourglass-medium fs-1"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                    @endforeach
                                    @if($soonToExpireVendors->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center align-middle">{{ __('messages.no_data_available') }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
           


            <div class="col-lg-6 col-md-6">
                <div class="card card-block card-height">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <h3 class="card-title">{{ __('messages.new_register_vendor') }}</h3>
                        
                        @if($newVendors->count() > 5)
                            <a href="{{ route('backend.vendors.index') }}">{{ __('messages.view_all') }}</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-primary">
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.registration_date') }}</th> 
                                    
                                </thead>
                                <tbody>
                                    @foreach($newVendors as $vendor)
                                        <tr>
                                            <td>
                                                <div class="d-flex gap-3 align-items-center">
                                                    <img src="{{ getSingleMedia(optional($vendor), 'profile_image', null) }}"
                                                        alt="avatar" class="avatar avatar-40 rounded-pill">
    
                                                    <div class="text-start">
                                                        <a href="{{ route('backend.vendors.details', $vendor->id) }}"
                                                            class="text-decoration-none">
                                                            <h6 class="m-0">{{ optional($vendor)->full_name ?? '-' }}</h6>
                                                        </a>
                                                        <span>{{ optional($vendor)->email ?? '--' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                         <td>{{ \App\Models\Setting::formatDate($vendor->created_at) }}</td> 
                                           
                                        </tr>
                                    @endforeach
                                    @if($newVendors->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center align-middle">{{ __('messages.no_data_available') }}</td>
                                    </tr>

                                    @endif
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="col-lg-6 col-md-6">
            <div class="card card-block card-height">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <h3 class="card-title">{{ __('messages.pending_collector_approval') }}</h3>
                    @if($pendingCollectors->count() > 5)
                        <a href="{{ route('backend.subscriptions.index') }}">{{ __('messages.view_all') }}</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.action') }}</th>
                            </thead>
                            <tbody>
                                @foreach($pendingCollectors as $collector)
                                    <tr>
                                        <td>
                                            <div class="d-flex gap-3 align-items-center">
                                                <img src="{{ getSingleMedia(optional($collector), 'profile_image', null) }}"
                                                    alt="avatar" class="avatar avatar-40 rounded-pill">
    
                                                <div class="text-start">
                                                    <a href="{{ route('backend.collectors.details', $collector->id) }}"
                                                        class="text-decoration-none">
                                                        <h6 class="m-0">{{ optional($collector)->full_name ?? '-' }}</h6>
                                                    </a>
                                                    <span>{{ optional($collector)->email ?? '--' }}</span>
                                               </div>
                                            </div>
                                        </td>
                                       
                                        <td>
                                            <a class="text-success fs-4 approve"
                                                href="{{ route('backend.approve', ['type' => 'collector', 'id' => $collector->id]) }}">
                                                <i class="ph ph-check fs-1"></i>
                                            </a>
                                            <a class="text-danger fs-4 reject" href="{{ route('backend.collectors.force_delete', $collector->id) }}">
                                                <i class="ph ph-x fs-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($pendingCollectors->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center align-middle">{{ __('messages.no_data_available') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

 
        <div class="col-lg-6 col-md-6">
            <div class="card card-block card-height">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <h3 class="card-title">{{ __('messages.pending_review_prescriptions') }}</h3>
                    @if($pendingPrescriptions->count() > 5)
                        <a href="{{ route('backend.subscriptions.index') }}">{{ __('messages.view_all') }}</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <th>{{ __('messages.lbl_customer_name') }}</th>
                                <th>{{ __('messages.prescription_date') }}</th>
                                <th>{{ __('messages.action') }}</th>
                            </thead>
                            <tbody>
                                @foreach($pendingPrescriptions as $prescription)
                                    <tr>
                                        
                                        @if($prescription->user !== null)
                                        <td>
                                            <div class="d-flex gap-3 align-items-center">
                                                <img src="{{ getSingleMedia(optional($prescription->user), 'profile_image', null) }}"
                                                    alt="avatar" class="avatar avatar-40 rounded-pill">

                                                <div class="text-start">
                                                    <a href="{{ route('backend.users.details', optional($prescription->user)->id) }}"
                                                        class="text-decoration-none">
                                                        <h6 class="m-0">{{ optional($prescription->user)->full_name ?? '-' }}</h6>
                                                    </a>
                                                    <span>{{ optional($prescription->user)->email ?? '--' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        @else
                                        <td>--</td>
                                        @endif
                                        <td>{{ \App\Models\Setting::formatDate($prescription->created_at) }}</td>
                                        <td>
                                            <a href="{{ route('backend.prescriptions.show', $prescription->id) }}" class="text-primary">
                                                <i class="ph ph-eye fs-1"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($pendingPrescriptions->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center align-middle">{{ __('messages.no_data_available') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection



@push('after-scripts')
    <script src="{{ asset('js/apexcharts.js') }}"></script>
    <script>

        const formatCurrencyvalue = (value) => {
            if (window.currencyFormat !== undefined) {
                return window.currencyFormat(value)
            }
            return value
        }


        revanue_chart('Year')

        var chart = null;
        let revenueInstance;
        function revanue_chart(type) {
            var Base_url = "{{ url('/') }}";
            var url = Base_url + "/app/get_revnue_chart_data/" + type;
            $("#revenue_loader").show();
            $.ajax({
                url: url,
                method: "GET",
                data: {},
                success: function (response) {
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
                            colors: ['#E50914'],
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                            },
                            grid: {
                                borderColor: '#404A51',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'],
                                    opacity: 0
                                },
                            },
                            xaxis: {
                                categories: category
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return formatCurrencyvalue(value); 
                                    }
                                }
                            },
                        };

                        if (revenueInstance) {
                            revenueInstance.updateOptions(options);
                        } else {
                            revenueInstance = new ApexCharts(document.querySelector("#chart-top-revenue"), options);
                            revenueInstance.render();
                        }
                    }
                }
            });
        }
        const chartDataUrl = "{{ route('backend.chart-data') }}";


        
        function createAppointmentsPieChart(appointmentsData) {
            const options = {
                series: [appointmentsData.testAppointments, appointmentsData.packageAppointments],
                chart: {
                    type: 'pie',
                    height: 180
                },
                labels: ['{{ __('messages.lbl_test') }}', '{{ __('messages.packages') }}'],
                legend: {
                    labels: {
                        colors: "var(--bs-body-color)" 
                    }
                },
                colors: ['var(--bs-primary)', 'var(--bs-secondary)'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            const chart = new ApexCharts(document.querySelector("#appointments-chart-tests-packages"), options);
            chart.render();
        }



       
        function showMessage(message) {
            Snackbar.show({
                text: message,
                pos: 'bottom-left'
            });
        }
       
        $(document).ready(function () {
    // Email button handlers
    $('.send-email-btn').on('click', function () {
        var userId = $(this).data('user-id'); 
        sendEmail(userId);  
    });
    
    $('#send-email-bulk').on('click', function () {
        sendEmail(); 
    });

    function sendEmail(userId = null) {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var data = {
            _token: csrfToken
        };

        if (userId) {
            data.user_id = userId; 
        }

        $.ajax({
            url: '{{ route('backend.send.email') }}',
            type: 'POST',
            data: data,
            success: function (response) {
                showMessage(response.message);
            },
            error: function (xhr, status, error) {
                console.error( error);
            }
        });
    }
});

        
        function loadMonthlyRevenueChart() {
            
            fetch("{{ route('backend.chart-data') }}?graph_name=monthlyRevenueTrend")
                .then(response => response.json())
                .then(data => {
                   
                    renderMonthlyRevenueChart(data);
                })
                .catch(error => console.error("{{ __('messages.error_fetching_monthly_revenue_data') }}", error));
           
        }

        function renderMonthlyRevenueChart(data) {
            let series = [];

           
            series.push({
                name: '{{ __('messages.commissions') }}',
                data: data.commissions.map(value => parseFloat(value) || 0), 
            });

            const hasSubscriptionRevenue = data.subscriptions.some(value => value > 0);

            if (hasSubscriptionRevenue) {
                series.push({
                    name: '{{ __('messages.subscriptions') }}',
                    data: data.subscriptions.map(value => parseFloat(value) || 0), 
                });
            }


            
            const options = {
                series: series,
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                },
                title: {
                    text: '{{ __('messages.monthly_revenue_trend') }}',
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: "var(--bs-heading-color)"
                        }
                    },
                    categories: data.months, 
                },
                yaxis: {
                    title: {
                        text: '{{ __('messages.total_revenue') }}'
                    },
                    labels: {
                        style: {
                            colors: ['var(--bs-heading-color)'],
                        },
                        formatter: function (value) {
                            return currencyFormat(value); 
                        }
                    }
                },
                colors: ['var(--bs-primary)', 'var(--bs-secondary)'],
                grid: {
                    borderColor: 'var(--bs-border-color)',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%', 
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                legend: {
                    labels: {
                        colors: 'var(--bs-body-color)' 
                    },
                    position: 'top'
                },
            };

           
            const chart = new ApexCharts(document.querySelector("#monthly-revenue-chart"), options);
            chart.render();
        }

        document.addEventListener("DOMContentLoaded", function () {
            const range_flatpicker = document.querySelectorAll('.dashboard-date-range')
          Array.from(range_flatpicker, (elem) => {
            if (typeof flatpickr !== typeof undefined) {
              flatpickr(elem, {
                mode: "range",
              })
            }
          })
            
            const appointmentsData = @json($appointmentsData);
            createAppointmentsPieChart(appointmentsData);
            loadMonthlyRevenueChart();
            




        });
    </script>

@endpush
<style>
    .star-rating {
        display: flex;
    }

    .star {
        font-size: 1.2rem;
        color: var(--bs-border-color);
        /* Default color for empty stars */
        margin-right: 2px;
    }

    .star.filled {
        color: var(--bs-warning);
        /* Color for filled stars */
    }
</style>