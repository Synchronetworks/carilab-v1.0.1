@extends('backend.layouts.app')

@section('title') {{ __($module_title) }}@endsection

@section('content')
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
            @if(multivendor() == 1 && auth()->user()->hasRole(['admin','demo_admin']))
            { 
                data: 'total_vendor_earning', 
                name: 'total_vendor_earning', 
                title: "{{ __('messages.lbl_vendor_earnings') }}",
                orderable: false,
                searchable: false
            },
            @endif
            { 
                data: 'total_collector_earning', 
                name: 'total_collector_earning', 
                title: "{{ __('messages.lbl_collector_earnings') }}",
                orderable: false,
                searchable: false
            },
            { 
                data: 'total_tax', 
                name: 'total_tax', 
                title: "{{ __('messages.lbl_tax') }}",
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




        let finalColumns = [
            ...columns,
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.reports.index_data") }}',
                finalColumns,
                orderColumn: [[ 4, "desc" ]],
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
