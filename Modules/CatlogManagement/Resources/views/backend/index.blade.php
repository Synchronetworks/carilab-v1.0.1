@extends('backend.layouts.app')

@section('title', $module_title)

@section('content')
    <div class="table-content">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                @if (auth()->user()->can('edit_catelog') ||
                        auth()->user()->can('delete_catelog'))
                    <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('edit_catelog')
                                    <option value="change-status">{{ __('messages.lbl_status') }}</option>
                                @endcan
                                @can('delete_catelog')
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                    @endcan
                                    @can('restore_catelog')
                                    <option value="restore">{{ __('messages.restore') }}</option>
                                    @endcan
                                    @can('force_delete_catelog')
                                    <option value="permanently-delete">{{ __('messages.permanent_dlt') }}</option>
                                @endcan
                            </select>
                        </div>
                        <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="form-select select2" id="status" style="width:100%">
                                <option value="1" selected>{{ __('messages.active') }}</option>
                                <option value="0">{{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                    </x-backend.quick-action>
                @endif


                <button type="button" class="btn btn-secondary flex-lg-grow-0 flex-grow-1" data-modal="export">
                    <i class="ph ph-export align-middle"></i> {{ __('messages.export') }}
                </button>
            </div>
            <x-slot name="toolbar">

                <div>
                    <div class="datatable-filter">
                        <select name="column_status" id="column_status" class="select2 form-control" data-filter="select"
                            style="width: 100%">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                {{ __('messages.inactive') }}</option>
                            <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                {{ __('messages.active') }}</option>
                        </select>
                    </div>
                </div>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text pe-0" id="addon-wrapping"><i
                            class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... " aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>
                @hasPermission('add_catelog')
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
            <div class="row gy-4">
                @if(multivendor() == 1 && auth()->user()->user_type != 'vendor')
                    {{-- Vendor Filter --}}
                    <div class="form-group">
                        <label class="form-label" for="vendor_id">{{ __('messages.lbl_vendor') }}</label>
                        <select name="filter_vendor" id="vendor_id" data-filter="select" class="select2 form-control"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'vendor']) }}" 
                            data-ajax--cache="true"
                            data-placeholder="{{ __('messages.select_vendor') }}">
                        </select>
                    </div>
                @endif
                {{-- Category Filter --}}
                <div class="form-group">
                    <label class="form-label" for="category_id">{{ __('messages.lbl_category') }}</label>
                    <select name="filter_category" id="category_id" data-filter="select" class="select2 form-control"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'category']) }}" 
                        data-ajax--cache="true"
                        data-placeholder="{{ __('messages.select_category') }}">
                    </select>
                </div>
        
                {{-- Test Type Filter --}}
                <div class="form-group">
                    <label class="form-label" for="test_type_id">{{ __('messages.lbl_test_type') }}</label>
                    <select name="filter_test_type" id="test_type_id" data-filter="select" class="select2 form-control"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'test_type']) }}" 
                        data-ajax--cache="true"
                        data-placeholder="{{ __('messages.select_case') }}">
                    </select>
                </div>
        
                {{-- Lab Filter --}}
                <div class="form-group">
                    <label class="form-label" for="lab_id">{{ __('messages.lbl_lab') }}</label>
                    <select name="filter_lab" id="lab_id" data-filter="select" class="select2 form-control"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'lab_name']) }}" 
                        data-ajax--cache="true"
                        data-placeholder="{{ __('messages.select_lab') }}">
                    </select>
                </div>
            </div>
        </form>
        <button type="reset" class="btn btn-danger mt-4" id="reset-filter">{{__('messages.reset')}}</button>
    </x-backend.advance-filter>
    @if (session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success"
                    onclick="dismissSnackbar(event)">{{__('messages.dismiss')}}</a>
            </div>
        </div>
    @endif
    @if (session('error'))
    <div class="snackbar" id="snackbar">
        <div class="d-flex justify-content-around align-items-center">
            <p class="mb-0">{{ session('error') }}</p>
            <a href="#" class="dismiss-link text-decoration-none text-danger" onclick="dismissSnackbar(event)">{{ __('messages.dismiss') }}</a>
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
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="catlogmanagement" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
                visible: {!! auth()->user()->can('edit_catelog') || auth()->user()->can('delete_catelog') ? 'true' : 'false' !!}

            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
            {
                data: 'name',
                name: 'name',
                title: "{{ __('messages.lbl_name') }}"
            },
            {
                data: 'category_id',
                name: 'category_id',
                title: "{{ __('messages.lbl_test_category') }}"
            },
            @if(multivendor() == 1 && auth()->user()->hasRole(['admin','demo_admin']))
            {
                data: 'vendor',
                name: 'vendor',
                title: "{{ __('messages.lbl_vendor') }}"
            },
            @endif
            {
                data: 'lab_id',
                name: 'lab_id',
                title: "{{ __('messages.labs') }}",
                orderable: false,
            },
            {
                data: 'price',
                name: 'price',
                title: "{{ __('messages.lbl_price') }}"
            },
           
            {
                data: 'status',
                name: 'status',
                title: "{{ __('messages.lbl_status') }}",
                width: '5%',
            },


        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.lbl_action') }}",
            width: '5%',
            visible: {!! auth()->user()->can('edit_catelog') || auth()->user()->can('delete_catelog') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data", ["lab_id" => $lab_id,"package_id" => $package_id]) }}',
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    return {
                        name: $('#user_name').val(),
                        vendor_id: $('#vendor_id').val(),
                        category_id: $('#category_id').val(),
                        test_type_id: $('#test_type_id').val(),
                        lab_id: $('#lab_id').val()
                       
                    }
                }
            });
        })

        $('#reset-filter').on('click', function(e) {
            $('#user_name').val('')
            $('#vendor_id').val('').trigger('change');
            $('#category_id').val('').trigger('change');
            $('#test_type_id').val('').trigger('change');
            $('#lab_id').val('').trigger('change');
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
