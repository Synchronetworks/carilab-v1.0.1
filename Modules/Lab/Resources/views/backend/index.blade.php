    @extends('backend.layouts.app')

    @section('title') {{__($module_title)}} @endsection


    @section('content')
    <div class="table-content">

        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                @if (auth()->user()->can('edit_lab') ||  auth()->user()->can('delete_lab'))
                                <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                                    <div class="">
                                        <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                                            style="width:100%">
                                            <option value="">{{ __('messages.no_action') }}</option>
                                            @can('edit_lab')
                                                <option value="change-status">{{ __('messages.lbl_status') }}</option>
                                            @endcan
                                            @can('delete_lab')
                                            <option value="delete">{{ __('messages.delete') }}</option>
                                            @endcan
                                            @can('restore_lab')
                                            <option value="restore">{{ __('messages.restore') }}</option>
                                            @endcan
                                            @can('force_delete_lab')
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
                        <select name="column_status" id="column_status" class="select2 form-control"
                            data-filter="select" style="width: 100%">
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
            <div class="row gy-4">
                <div class="form-group">
                    <label class="form-label" for="lab_name"> {{ __('messages.lbl_lab_name') }} </label>
                    <select name="filter_lab_id" id="lab_name" data-filter="select"
                        class="select2 form-select"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'lab_name']) }}" 
                        data-ajax--cache="true"
                        data-placeholder="{{ __('messages.select_lab') }}">
                    </select>
                </div>
                @if(multivendor() == 1 && auth()->user()->user_type != 'vendor')
                <div class="form-group">
                    <label class="form-label" for="filter_vendor"> {{ __('messages.lbl_vendorName') }} </label>
                    <select name="filter_vendor" id="filter_vendor" data-filter="select" class="form-select select2" 
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'vendor']) }}" 
                            data-ajax--cache="true" data-placeholder="{{ __('messages.select_vendor') }}">
                    </select>
                </div>
                @endif  
                {{-- Collector Filter --}}
                <div class="form-group">
                    <label class="form-label" for="filter_collector">{{ __('messages.lbl_collector_name') }}</label>
                    <select name="filter_collector" id="filter_collector" data-filter="select" class="form-select select2"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'collector_name']) }}" 
                            data-ajax--cache="true" data-placeholder="{{ __('messages.select_collector') }}">
                    </select>
                </div>
    
                {{-- Taxes Filter --}}
                <div class="form-group">
                    <label class="form-label" for="filter_tax">{{ __('messages.lbl_taxes') }}</label>
                    <select name="filter_tax" id="filter_tax" data-filter="select" class="form-select select2"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'tax']) }}" 
                            data-ajax--cache="true" data-placeholder="{{ __('messages.select_tax') }}">
                    </select>
                </div>
            
                 {{-- Accreditation Type Filter --}}
                <div class="form-group">
                    <label class="form-label" for="filter_accreditation">{{ __('messages.lbl_accreditation_type') }}</label>
                    <select name="filter_accreditation" id="filter_accreditation" data-filter="select" class="form-select select2">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="ISO">{{ __('messages.iso') }}</option>
                        <option value="NABL">{{ __('messages.inbl') }}</option>
                    </select>
                </div>
    
                {{-- Payment Mode Filter --}}
                <div class="form-group">
                    <label class="form-label" for="filter_payment_mode">{{ __('messages.lbl_payment_mode') }}</label>
                    <select name="filter_payment_mode" id="filter_payment_mode" data-filter="select" class="form-select select2">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="manual">{{ __('messages.manual') }}</option>
                        <option value="online">{{ __('messages.online') }}</option>
                    </select>
                </div>
                <button type="reset" class="btn btn-danger mt-4" id="reset-filter">{{__('messages.reset')}}</button>
            </div>
        </form>
    </x-backend.advance-filter>
    @if (session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">{{__('messages.dismiss')}}</a>
            </div>
        </div>
    @endif
    @if (session('error'))
    <div class="snackbar" id="snackbar">
        <div class="d-flex justify-content-around align-items-center">
            <p class="mb-0">{{ session('error') }}</p>
            <a href="#" class="dismiss-link text-decoration-none text-danger" onclick="dismissSnackbar(event)">{{__('messages.dismiss')}}</a>
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
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="lab" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
                visible: {!! auth()->user()->can('edit_lab') || auth()->user()->can('delete_lab') ? 'true' : 'false' !!}

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
                title: "{{ __('messages.lbl_name') }}",
            },
           
            ...(@json(multivendor()) == 1 && @json(auth()->user()->user_type) != 'vendor' ? [{
                data: 'vendor',
                name: 'vendor',
                title: "{{ __('messages.lbl_vendor') }}"
            }] : []),
            {
                data: 'test_case_counter',
                name: 'test_case_counter',
                title: "{{ __('messages.test_case_counter') }}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'booking_count',
                name: 'bookong_count',
                title: "{{__('messages.bookings')}}",
                orderable: false,
                searchable: false,
            },
            {
                data: 'collectors_count',
                name: 'collectors_count',
                title: "{{__('messages.collectors')}}",
                orderable: false,
                searchable: false,
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
                visible: {!! auth()->user()->can('edit_lab') || auth()->user()->can('delete_lab') ? 'true' : 'false' !!}

            }]

            let finalColumns = [
                ...columns,
                ...actionColumn
            ]


    document.addEventListener('DOMContentLoaded', () => {
    function checkFilters() {
        let hasFilters = false;

        $('.datatable-filter select').each(function () {
            if ($(this).val() && $(this).val() !== '') {
                hasFilters = true;
                return false; 
            }
        });

        $('#reset-filter').toggle(hasFilters);
    }

    checkFilters();
    initDatatable({
        url: '{{ route("backend.$module_name.index_data") }}',
        finalColumns,
        orderColumn: [
            [1, "desc"]
        ],
        advanceFilter: () => {
            return {
                lab_id: $('#lab_name').val(),
                vendor_id: '{{ $vendor_id }}',
                vendor_name: $('#filter_vendor').val(),
                collector_id: $('#filter_collector').val(),
                tax_id: $('#filter_tax').val(),
                accreditation_type: $('#filter_accreditation').val(),
                payment_mode: $('#filter_payment_mode').val(),
            }
        }
    });
    $('#reset-filter').on('click', function (e) {
        $('#lab_name').val('').trigger('change');
        e.preventDefault();    
        $('.datatable-filter select').val(null).trigger('change');

        
        $(this).hide();  
        window.renderedDataTable.ajax.reload(null, false);
    });

    $('.datatable-filter select').on('change', function () {
        checkFilters();
    });
});

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

            $('#quick-action-type').change(function () {
                resetQuickAction()
            });

            $(document).on('update_quick_action', function () {
                // resetActionButtons()
            })

            $(document).on('change', '#column_country', function() {
           var country = $(this).val();
            $('#column_state').empty();
            $('#column_city').empty();
            stateName(country);
        })


        $(document).on('change', '#column_state', function() {
           var state = $(this).val();
           $('#column_city').empty();
           cityName(state);
        })


    function stateName(country) {
        var state = $('#column_state');
       var state_route = "{{ route('backend.get_search_data', [ 'type' => 'state','sub_type' =>'']) }}" + country;
       state_route = state_route.replace('amp;', '');
       $.ajax({
            url: state_route,
            success: function(result) {

            $('#column_state').empty();

            $.each(result.results, function(index, state) {
                $('#column_state').append($('<option>', {
                    value: state.id,
                    text: state.text
                }));
            });

            $('#column_state').select2({
                width: '100%'
            });


            if (country) {
                $("#column_state").val(state).trigger('change');
            }
         }
      });
    }



    function cityName(state) {
        var city = $('#column_city');
        var city_route = "{{ route('backend.get_search_data', [ 'type' => 'city','sub_type' =>'']) }}" + state;
        city_route = city_route.replace('amp;', '');
        $.ajax({
             url: city_route,
             success: function(result) {

             $('#column_city').empty();

             $.each(result.results, function(index, city) {
                 $('#column_city').append($('<option>', {
                     value: city.id,
                     text: city.text
                 }));
             });

             $('#column_city').select2({
                 width: '100%'
             });


             if (state) {
                 $("#column_city").val(city).trigger('change');
             }
          }
       });
     }
        </script>
    @endpush