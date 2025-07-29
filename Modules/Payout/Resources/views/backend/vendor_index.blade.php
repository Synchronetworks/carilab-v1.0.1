@extends('backend.layouts.app')

@section('title')  {{ __($module_title) }}  @endsection

@section('content')
    <div class="table-content">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
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
                    
                <button class="btn btn-secondary d-flex justify-content-center align-items-center gap-1 btn-group" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
    </div>
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4 class="mb-0">{{ __('messages.lbl_advanced_filter') }}</h4>
        </x-slot>
        <form action="javascript:void(0)" class="datatable-filter">
            <div class="form-group">
                <label class="form-label" for="filter_vendor"> {{ __('messages.lbl_vendorName') }} </label>
                <select name="filter_vendor" id="filter_vendor" data-filter="select" class="form-select select2" 
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'vendor']) }}" 
                        data-ajax--cache="true" data-placeholder="{{ __('messages.select_vendor') }}">
                </select>
            </div>
            <div class="form-group mt-3">
                <label class="form-label" for="filter_payment_method"> {{ __('messages.lbl_payment_method') }} </label>
                <select name="filter_payment_method" id="filter_payment_method" class="form-select select2" data-filter="select">
                    <option value="">{{ __('messages.choose_payment_method') }}</option>
                    <option value="bank">{{ __('messages.bank') }}</option>
                    <option value="cash">{{ __('messages.cash') }}</option>
                    <option value="wallet">{{ __('messages.wallet') }}</option>
                </select>
            </div>
        </form>
        <button type="reset" class="btn btn-danger mt-4" id="reset-filter">{{__('messages.reset')}}</button>
    </x-backend.advance-filter>
    @if(session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">{{__('messages.dismiss')}} </a>
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
                title: "{{ __('messages.lbl_vendor') . ' ' . __('messages.lbl_name') }}"
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

        ]

        let finalColumns = [
            ...columns,
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.payouts.vendor_index_data") }}',
                finalColumns,
                orderColumn: [[ 4, "desc" ]],
                advanceFilter: () => {
                    return {
                        vendor_name: $('#filter_vendor').val(),
                        payment_method: $('#filter_payment_method').val(),
                        
                    }
                }
            });
        })

        $('#reset-filter').on('click', function(e) {
            $('#filter_vendor').val('').trigger('change');
            $('#filter_payment_method').val('').trigger('change');
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
