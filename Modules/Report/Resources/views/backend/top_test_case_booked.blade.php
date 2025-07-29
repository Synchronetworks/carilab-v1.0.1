@extends('backend.layouts.app')

@section('title') {{ __($module_title) }}@endsection

@section('content')
    <div class="table-content">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3 justify-content-lg-start justify-content-between">
                <button type="button" class="btn btn-secondary flex-lg-grow-0 flex-grow-1" data-modal="export">
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
                data: 'test_case', 
                name: 'test_case', 
                title: "{{ __('messages.lbl_test_case') }}", 

            },
            { 
                data: 'test_category', 
                name: 'test_category', 
                title: "{{ __('messages.lbl_test_category') }}", 
            },
            { 
                data: 'appointments_count', 
                name: 'appointments_count', 
                title: "{{ __('messages.lbl_booking_counts') }}", 
                searchable: false,
            },
            { 
                data: 'booking_percentage', 
                name: 'booking_percentage', 
                title: "{{ __('messages.lbl_booking_percentage') }}",
                searchable: false,

            },
            { 
                data: 'last_booking_date', 
                name: 'last_booking_date', 
                title: "{{ __('messages.lbl_last_booking_date') }}",
                orderable: true,

            },
            

        ]
        let finalColumns = [
            ...columns,
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.reports.top_testcase_booked_data") }}',
                finalColumns,
                orderColumn: [[ 0, "desc" ]],
                advanceFilter: () => {
                    return {
                        name: $('#user_name').val()
                    }
                }
            });
        })


</script>
@endpush
