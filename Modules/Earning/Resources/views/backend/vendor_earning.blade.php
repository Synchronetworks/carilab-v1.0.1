@extends('backend.layouts.app')

@section('title'){{__($module_title)}}@endsection


@section('content')
    <div class="table-content">
        <x-backend.section-header>
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
    
    @if(session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">{{__('messages.dismiss')}}</a>
            </div>
        </div>
    @endif
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
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('messages.lbl_name') }}",
                orderable: true, 
                searchable: true
            },
            { 
                data: 'total_appointment', 
                name: 'total_appointment', 
                title: "{{ __('messages.lbl_tot_appointment') }}", 
                orderable: false,
                searchable: false
            },
            { 
                data: 'total_service_amount', 
                name: 'total_service_amount', 
                title: "{{ __('messages.lbl_total_amount') }}", 
                orderable: false,
                searchable: false
            },
            { 
                data: 'total_admin_earning', 
                name: 'total_admin_earning', 
                title: "{{ __('messages.lbl_admin_earnings') }}", 
                orderable: false, 
                searchable: false 
            },
            { 
                data: 'vendor_pay_due', 
                name: 'vendor_pay_due', 
                title: "{{ __('messages.lbl_vendor_earnings') }}",
                orderable: false,
                searchable: false
            },
            {
                data:'vendor_paid_earning',
                name:'vendor_paid_earning',
                title: "{{ __('messages.lbl_vendor_paid_earning') }}"
            },
            { 
                data: 'total_collector_earning', 
                name: 'total_collector_earning', 
                title: "{{ __('messages.lbl_collector_earnings') }}",
                orderable: false,
                searchable: false
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
           },

        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.lbl_action') }}",
            width: '5%',
            visible:  {!! auth()->user()->hasRole(['admin','demo_admin']) ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.earnings.vendor_index_data") }}',
                finalColumns,
                orderColumn: [[ 4, "desc" ]],
                advanceFilter: () => {
                    return {
                        name: $('#user_name').val()
                    }
                }
            });
        })

      
</script>
@endpush
