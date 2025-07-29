@extends('backend.layouts.app')

@section('title',__('messages.subscriptions'))



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
                    <span class="input-group-text pe-0" id="addon-wrapping"><i
                            class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... "
                        aria-label="Search" aria-describedby="addon-wrapping">
                </div>

            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive"></table>
    </div>

    @if (session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success"
                    onclick="dismissSnackbar(event)">{{__('messages.dismiss')}}</a>
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
        const columns = [
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.update_at') }}",
                orderable: true,
                visible: false,
            },
            {
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('messages.lbl_user') }}"
            },
            {
                data: 'name',
                name: 'name',
                title: "{{ __('messages.plan') }}"
            },
            {   data: 'duration',
                name: 'duration',
                title: "{{ __('messages.lbl_duration') }}" 
            },
            {
                data: 'start_date',
                name: 'start_date',
                title: "{{ __('messages.lbl_purchase_date') }}"
            },
            {
                data: 'end_date',
                name: 'end_date',
                title: "{{ __('messages.lbl_expiry_date') }}"
            },
         
            {
                data: 'total_amount',
                name: 'total_amount',
                title: "{{ __('messages.lbl_amount') }}"
            },
            
            {
                data: 'status',
                name: 'status',
                title: "{{ __('messages.lbl_status') }}",
                render: function(data, type, row) {
                    let capitalizedData = data.charAt(0).toUpperCase() + data.slice(1);
                    let className = data == 'active' ? 'badge bg-success-subtle p-2' : 'badge bg-danger-subtle p-2';
                    return '<span class="' + className + '">' + capitalizedData + '</span>';
                }
            },
            
        ];

        const actionColumn = [
            
        ];


        const finalColumns = [...columns, ...actionColumn];

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                search: {
                    selector: '.dt-search',
                    smart: true
                }
            });
        });

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            $('#quick-action-apply').attr('disabled', actionValue == '');
            $('.quick-action-field').addClass('d-none');
            if (actionValue == 'change-status') {
                $('#change-status-action').removeClass('d-none');
            }
        }

        $('#quick-action-type').change(resetQuickAction);
    </script>
@endpush
