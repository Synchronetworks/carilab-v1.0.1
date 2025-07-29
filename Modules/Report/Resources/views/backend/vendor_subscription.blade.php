@extends('backend.layouts.app')

@section('title',__('messages.vendor_subscription'))

@section('content')
    <div class="card">
        <div class="card-body">
            @if(auth()->user()->hasRole(['admin','demo_admin']))
            <div class="row gy-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-body mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="text-primary">
                                    {{ $totalsubscription ?? 0 }}
                                </h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-book-open-user"></i>
                                </div>
                            </div>
                            <h6 class="mb-0">{{ __('messages.total_subscription') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-body mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="text-primary">{{ $totalActivesubscription ?? 0 }}</h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-crown"></i>
                                </div>
                            </div>
                            <h6 class="mb-0">{{ __('messages.active_subscription') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-body mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="text-primary">
                                    {{ $totalExpiredsubscription ?? 0 }}
                                </h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-calendar-x"></i>
                                </div>
                            </div>
                            <h6 class="mb-0">{{ __('messages.expired_subscription') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-body mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <h3 class="text-primary">{{ $totalExpiredSoonsubscription ?? 0 }}</h3>
                                <div class="icon-50 badge rounded-circle bg-primary fs-2">
                                    <i class="ph ph-hourglass"></i>
                                </div>
                            </div>
                            <h6 class="mb-0">{{ __('messages.expired_soon') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            @endif
    </div>
</div>
<div class="table-content">
        <x-backend.section-header>   
            <div class="d-flex flex-wrap gap-3 justify-content-lg-start justify-content-between">
                <button type="button" class="btn btn-secondary" data-modal="export">
                    <i class="ph ph-export align-middle"></i> {{ __('messages.export') }}
                </button>
            </div>              
            <x-slot name="toolbar">
                <div class="input-group flex-nowrap">
                    <span class="input-group-text pe-0" id="addon-wrapping"><i
                            class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... " aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>
            </x-slot>
        </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
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
        const columns = [
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
           },
            { 
                data: 'user_id', 
                name: 'user_id', 
                title: "{{ __('messages.lbl_vendor') }}", 
            },
            { 
                data: 'plan', 
                name: 'plan', 
                title: "{{ __('messages.lbl_plan') }}", 
            },
            {   data: 'duration',
                name: 'duration',
                title: "{{ __('messages.lbl_duration') }}" 
            },
            { 
                data: 'total_amount', 
                name: 'total_amount', 
                title: "{{ __('messages.lbl_total_amount') }}", 
            },
            { 
                data: 'start_date', 
                name: 'start_date', 
                title: "{{ __('messages.lbl_start_date') }}", 
            },
            { 
                data: 'end_date', 
                name: 'end_date', 
                title: "{{ __('messages.lbl_end_date') }}", 
            },
            { 
                data: 'status', 
                name: 'status', 
                title: "{{ __('messages.lbl_status') }}", 
                render: function(data, type, row) {
                    let capitalizedData = data.charAt(0).toUpperCase() + data.slice(1);
                    let className = data == 'active' || data == '1' ? 'badge bg-success-subtle p-2' : 'badge bg-danger-subtle p-2';
                    return '<span class="' + className + '">' + capitalizedData + '</span>';
                }
            },
            
        ]




        let finalColumns = [
            ...columns,
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.reports.vendor_subscription_data") }}',
                finalColumns,
                orderColumn: [[ 1, "desc" ]],
                advanceFilter: () => {
                    return {
                        name: $('#user_name').val()
                    }
                }
            });
        })

        $('#reset-filter').on('click', function(e) {
            $('#user_name').val('')
            window.renderedDataTable.ajax.reload(null, false)
        })
        function resetQuickAction () {
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

      $('#quick-action-type').change(function () {
        resetQuickAction()
      });

     
</script>
@endpush
