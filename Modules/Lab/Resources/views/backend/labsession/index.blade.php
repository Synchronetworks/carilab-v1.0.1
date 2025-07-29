@extends('backend.layouts.app')

@section('title') {{__($module_title)}} @endsection


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
                @hasPermission('add_lab')
                    <a href="{{ route('backend.' . $module_name . '.create') }}"
                        class="btn btn-primary d-flex justify-content-center align-items-center gap-1" id="add-post-button"> <i
                            class="ph ph-plus-circle"></i>{{ __('messages.new') }}</a>
                @endhasPermission
                <button class="btn btn-secondary d-flex justify-content-center align-items-center gap-1 btn-group" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i
                        class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}</button>
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
                <label class="form-label" for="lab_name"> {{ __('messages.lbl_lab_name') }} </label>
                <select name="filter_lab_id" id="lab_name" data-filter="select"
                    class="select2 form-select"
                    data-ajax--url="{{ route('backend.get_search_data', ['type' => 'lab_name']) }}" 
                    data-ajax--cache="true"
                    data-placeholder="{{ __('messages.select_lab') }}">
                </select>
            </div>
        </form>
        <button type="reset" class="btn btn-danger mt-4" id="reset-filter">{{__('messages.reset')}} </button>
    </x-backend.advance-filter>
    @if (session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success"
                    onclick="dismissSnackbar(event)">{{__('messages.dismiss')}} </a>
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
        const columns = [
            // {
            //    name: 'check',
            //    data: 'check',
            //    title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="labsession" onclick="selectAllTable(this)">',
            //    width: '0%',
            //    exportable: false,
            //    orderable: false,
            //    searchable: false,
            //    visible: {!! auth()->user()->can('edit_lab') || auth()->user()->can('delete_lab') ? 'true' : 'false' !!}
            
            // },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
            {
                data: 'lab_id',
                name: 'lab_id',
                title: "{{ __('messages.lbl_name') }}"
            },
            {
                data: 'day',
                name: 'day',
                title: "{{ __('messages.lbl_day') }}",
            },



        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.lbl_action') }}",
            width: '5%',
            visible: {!! auth()->user()->can('edit_lab') || auth()->user()->can('delete_lab') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    return {
                        lab_id: $('#lab_name').val(),
                    }
                }
            });
        })

        $('#reset-filter').on('click', function(e) {
            $('#lab_name').val('').trigger('change');
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

        $(document).on('update_quick_action', function() {
            // resetActionButtons()
        })
    </script>
@endpush
