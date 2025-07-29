@extends('backend.layouts.app')

@section('title', $module_title)

@section('content')
    <div class="table-content">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-2">

                <button type="button" class="btn btn-secondary flex-lg-grow-0 flex-grow-1" data-modal="export">
                    <i class="ph ph-export align-middle"></i> {{ __('messages.export') }}
                </button>
            </div>
            <x-slot name="toolbar">

                <div class="input-group flex-nowrap">
                    <span class="input-group-text pe-0" id="addon-wrapping"><i class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... "
                        aria-label="Search" aria-describedby="addon-wrapping">
                </div>


            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive appointment-table">
        </table>
    </div>

    @if (session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success"
                    onclick="dismissSnackbar(event)">{{ __('messages.dismiss') }}</a>
            </div>
        </div>
    @endif
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush
@push('after-scripts')
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>
    <script src="{{ asset('js/form/index.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script type="text/javascript" defer>
        const columns = [{
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                searchable: false,
                visible: false,
            },
            {
                data: 'id',
                name: 'id',
                title: "{{ __('messages.id') }}",
            },
            {
                data: 'appointment_id',
                name: 'appointment_id',
                title: "{{ __('messages.lbl_test_case') }}",
                searchable: true,

            },
            {
                data: 'customer_id',
                name: 'customer_id',
                title: "{{ __('messages.lbl_customer') }}"
            },
            @if (!isset($payment_type) || $payment_type !== 'cash')
                {
                    data: 'payment_type',
                    name: 'payment_type',
                    title: "{{ __('messages.payment_type') }}"
                },
            @endif
            @if (isset($payment_type) && $payment_type == 'cash')
                {
                    data: 'cash_history',
                    name: 'cash_history',
                    title: "{{ __('messages.cash_history') }}",
                    orderable: false

                },
            @endif

            {
                data: 'payment_status',
                name: 'payment_status',
                title: "{{ __('messages.status') }}"
            },
            {
                data: 'datetime',
                name: 'datetime',
                title: "{{ __('messages.datetime') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'total_amount',
                name: 'total_amount',
                title: "{{ __('messages.total_paid_amount') }}",

            },

        ]

        const actionColumn = [
            ...(@json(auth()->user()->user_type) == 'admin' || @json($payment_type ?? '') == 'cash' ? [{
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                title: "{{ __('messages.lbl_action') }}",
                width: '5%',


            }] : []),
        ]



        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            let payment_type = '{{ request()->route('payment_type') ?? '' }}';

            initDatatable({
                url: '{{ route('backend.payments.index_data') }}' + '?payment_type=' + payment_type,
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    return {


                    }
                }
            });
        })

        $('#reset-filter').on('click', function(e) {


            window.renderedDataTable.ajax.reload(null, false)
        })

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
            resetQuickAction()
        });
    </script>
@endpush
