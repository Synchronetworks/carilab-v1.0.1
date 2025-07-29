@extends('backend.layouts.app')


@section('title')
    {{ __($module_title) }}
@endsection



@section('content')
    <div class="table-content">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3 justify-content-lg-start justify-content-between">
                @if (auth()->user()->can('edit_bookings') || auth()->user()->can('delete_bookings'))
                    <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>

                                @can('delete_bookings')
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                @endcan
                                @can('restore_bookings')
                                    <option value="restore">{{ __('messages.restore') }}</option>
                                @endcan
                                @can('force_delete_bookings')
                                    <option value="permanently-delete">{{ __('messages.permanent_dlt') }}</option>
                                @endcan
                            </select>
                        </div>

                    </x-backend.quick-action>
                @endif


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
                @hasPermission('add_bookings')
                    <a href="{{ route('backend.' . $module_name . '.create') }}"
                        class="btn btn-primary d-flex justify-content-center align-items-center gap-1" id="add-post-button"> <i
                            class="ph ph-plus-circle"></i>{{ __('messages.new') }}</a>
                @endhasPermission
                <button class="btn btn-secondary d-flex justify-content-center align-items-center gap-1 btn-group"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i
                        class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}</button>
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive appointment-table">
        </table>
    </div>
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4 class="mb-0">{{ __('messages.lbl_advanced_filter') }}</h4>
        </x-slot>
        <form action="javascript:void(0)" class="datatable-filter">
            <div class="row gy-4">
                <div class="form-group">
                    <label class="form-label" for="collector"> {{ __('messages.lbl_collector_name') }} </label>
                    <select name="filter_collector_id" id="collector" data-filter="select" class="select2 form-select"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'collector_name']) }}"
                        data-ajax--cache="true" data-placeholder="{{ __('messages.select_collector') }}">
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="lab_name"> {{ __('messages.lbl_lab_name') }} </label>
                    <select name="filter_lab_id" id="lab_name" data-filter="select" class="select2 form-select"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'lab_name']) }}"
                        data-ajax--cache="true" data-placeholder="{{ __('messages.select_lab') }}">
                    </select>
                </div>

                @if (multivendor() == 1 && auth()->user()->user_type != 'vendor')
                    <div class="form-group">
                        <label for="vendor_name" class="form-label"> {{ __('messages.lbl_vendorName') }} </label>
                        <select name="filter_vendor_id" id="vendor_name" data-filter="select" class="select2 form-select"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'vendor']) }}"
                            data-ajax--cache="true" data-placeholder="{{ __('messages.select_vendor') }}">
                        </select>
                    </div>
                @endif
                <div class="form-group">
                    <label for="test_name" class="form-label"> {{ __('messages.lbl_test_case') }} </label>
                    <select name="filter_test" id="test_name" data-filter="select" class="select2 form-select"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'test']) }}" data-ajax--cache="true"
                        data-placeholder="{{ __('messages.select_test') }}">
                    </select>
                </div>
                <div class="form-group">
                    <label for="payment_status" class="form-label">{{ __('messages.lbl_payment_status') }}</label>
                    <select name="filter_payment_status" id="payment_status" data-filter="select"
                        class="select2 form-select" data-placeholder="{{ __('messages.select_payment_status') }}">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="paid">{{ __('messages.paid') }}</option>
                        <option value="pending">{{ __('messages.pending') }}</option>
                        <option value="failed">{{ __('messages.failed') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="booking_status">{{ __('messages.lbl_status') }}</label>
                    <select name="filter_column_status" id="booking_status" data-filter="select"
                        class="select2 form-select" data-placeholder="{{ __('messages.select_status') }}">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="pending">{{ __('messages.pending') }}</option>
                        <option value="accept">{{ __('messages.accept') }}</option>
                        <option value="on_going">{{ __('messages.on_going') }}</option>
                        <option value="in_progress">{{ __('messages.in_progress') }}</option>
                        <option value="cancelled">{{ __('messages.cancelled') }}</option>
                        <option value="completed">{{ __('messages.completed') }}</option>
                        <option value="pending_approval">{{ __('messages.pending_approval') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="submission_status">{{ __('messages.submission_status') }}</label>
                    <select name="filter_submission_status" id="submission_status" data-filter="select"
                        class="select2 form-select" data-placeholder="{{ __('messages.select_submission_status') }}">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="awaiting_validation">{{ __('messages.awaiting_validation') }}</option>
                        <option value="in_progress">{{ __('messages.in_progress') }}</option>
                        <option value="validated">{{ __('messages.validated') }}</option>
                        <option value="report_generated">{{ __('messages.report_generated') }}</option>
                    </select>
                </div>
            </div>
        </form>
        <button type="reset" class="btn btn-danger mt-4" id="reset-filter">{{ __('messages.reset') }}</button>
    </x-backend.advance-filter>
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
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush
@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>
    <script src="{{ asset('js/form/index.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script type="text/javascript" defer>
        const columns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="appointment" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
                visible: {!! auth()->user()->can('edit_bookings') || auth()->user()->can('delete_bookings') ? 'true' : 'false' !!}

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
                title: "{{ __('messages.id') }}"
            },
            {
                data: 'appointment_date',
                name: 'appointment_date',
                title: "{{ __('messages.datetime') }}",
                orderable: true,
            },
            {
                data: 'customer',
                name: 'customer',
                title: "{{ __('messages.lbl_customer') }}",
                orderable: true,
            },
            {
                data: 'lab',
                name: 'lab',
                title: "{{ __('messages.lbl_lab') }}",
                orderable: true,
            },
            {
                data: 'collector',
                name: 'collector',
                title: "{{ __('messages.lbl_collector') }}",
                orderable: true,
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

            },

        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.lbl_action') }}",
            width: '5%',
            visible: {!! auth()->user()->can('edit_bookings') || auth()->user()->can('delete_bookings') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data", ['collectorId' => $collector_id, 'lab_id' => $lab_id, 'vendorId' => $vendor_id]) }}',
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    return {
                        collector_id: $('#collector').val(),
                        lab_id: $('#lab_name').val(),
                        vendor_id: $('#vendor_name').val(),
                        test_id: $('#test_name').val(),
                        user_name: $('#user_name').val(),
                        status: $('#column_status').val(),
                        payment_status: $('#payment_status').val(),
                        status: $('#booking_status').val(),
                        payment_type: $('#payment_type').val(),
                        submission_status: $('#submission_status').val(),

                    }
                }
            });
        })

        $('#reset-filter').on('click', function(e) {

            $('#collector').val('').trigger('change');
            $('#lab_name').val('').trigger('change');
            $('#vendor_name').val('').trigger('change');
            $('#test_name').val('').trigger('change');
            $('#user_name').val('').trigger('change');
            $('#column_status').val('').trigger('change');
            $('#booking_status').val('').trigger('change');
            $('#payment_status').val('').trigger('change');
            $('#status').val('').trigger('change');
            $('#submission_status').val('').trigger('change');
            $('#payment_type').val('').trigger('change');

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


        function checkFilters() {
            let hasFilter = false;

            $('.datatable-filter select').each(function() {
                if ($(this).val() !== '' && $(this).val() !== null) {
                    hasFilter = true;
                }
            });

            if (hasFilter) {
                $('#reset-filter').show();
            } else {
                $('#reset-filter').hide();
            }
        }

        $(document).ready(function() {
            checkFilters();
        });

        $('.datatable-filter select').on('change', function() {
            checkFilters();
        });

        $('#reset-filter').on('click', function() {
            $('.datatable-filter select').val('').trigger('change');
            window.renderedDataTable.ajax.reload(null, false);
            checkFilters();
        });
    </script>
@endpush
