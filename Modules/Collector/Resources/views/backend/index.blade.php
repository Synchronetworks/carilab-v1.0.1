@extends('backend.layouts.app')

@section('title', isset($approval_status) && in_array($approval_status, ['pending', 'unassigned']) 
    ? ($approval_status == 'pending' ? __('messages.pending') . ' ' . __('messages.collector_list') 
    : __('messages.unassigned_collector_list')) 
    : __('messages.collector_list'))

@section('content')

    <div class="table-content">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                @if (auth()->user()->can('edit_collector') ||
                        auth()->user()->can('delete_collector'))
                    <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('edit_collector')
                                    <option value="change-status">{{ __('messages.lbl_status') }}</option>
                                @endcan
                                @can('delete_collector')
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                    @endcan
                                    @can('restore_collector')
                                    <option value="restore">{{ __('messages.restore') }}</option>
                                    @endcan
                                    @can('force_delete_collector')
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
                    @if(request()->route('approval_status') != 'pending'  )
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
                    @endif
                </div>

                <div class="input-group flex-nowrap">
                    <span class="input-group-text pe-0" id="addon-wrapping"><i
                            class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... " aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>
                @hasPermission('add_collector')
                    @if (!request()->route('approval_status'))
                        <a href="{{ route('backend.' . $module_name . '.create') }}"
                            class="btn btn-primary d-flex justify-content-center align-items-center gap-1" id="add-post-button"><i
                                class="ph ph-plus-circle"></i>{{ __('messages.new') }}</a>
                    @endif
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
                <div class="form-group datatable-filter">
                    <label class="form-label" for="collector_name">{{ __('messages.lbl_collector_name') }}</label>
                    <select id="collector_name" name="collector_name" data-filter="select" class="select2 form-control"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'collector_name']) }}" 
                            data-ajax--cache="true"
                            data-placeholder="{{ __('messages.select_collector') }}">
                    </select>
                </div>   
                @if(multivendor() == 1 && auth()->user()->user_type != 'vendor')    
                <div class="form-group">
                    <label class="form-label" for="filter_vendor">{{ __('messages.lbl_vendorName') }}</label>
                    <select name="filter_vendor" id="filter_vendor" data-filter="select" class="form-select select2"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'vendor']) }}" 
                            data-ajax--cache="true"
                            data-placeholder="{{ __('messages.select_vendor') }}">
                    </select>
                </div> 
                @endif      
                <div class="form-group datatable-filter">
                    <label class="form-label" for="lab_name">{{ __('messages.lbl_lab_name') }}</label>
                    <select id="lab_name" name="lab_name" data-filter="select" class="select2 form-control"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'lab_name']) }}" 
                            data-ajax--cache="true"
                            data-placeholder="{{ __('messages.select_lab') }}">
                    </select>
                </div>       
                <div class="form-group datatable-filter">
                    <label class="form-label">{{ __('messages.lbl_gender') }}<span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center flex-sm-nowrap flex-wrap gap-3">
                        <label class="form-check form-control px-5 cursor-pointer">
                            <div>
                                <input class="form-check-input" type="radio" name="gender" id="male" value="male"
                                    {{ old('gender') == 'male' ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('messages.lbl_male') }}</span>
                            </div>
                        </label>
                        <label class="form-check form-control px-5 cursor-pointer">
                            <div>
                                <input class="form-check-input" type="radio" name="gender" id="female" value="female"
                                    {{ old('gender') == 'female' ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('messages.lbl_female') }}</span>
                            </div>
                        </label>
                        <label class="form-check form-control px-5 cursor-pointer">
                            <div>
                                <input class="form-check-input" type="radio" name="gender" id="other" value="other"
                                    {{ old('gender') == 'other' ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('messages.lbl_other') }}</span>
                            </div>
                        </label>
                    </div>
                    @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
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
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="users" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
                visible: {!! auth()->user()->can('edit_collectors') || auth()->user()->can('delete_collectors') ? 'true' : 'false' !!}

            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{__('messages.lbl_update_at')}}",
                orderable: true,
                visible: false,
            },
            {
                data: 'name',
                name: 'name',
                title: "{{__('messages.collector')}}"
            },
            ...(@json(multivendor()) == 1 && @json(auth()->user()->user_type) != 'vendor' ? [{
                data: 'vendor_name',
                name: 'vendor_name',
                title: "{{ __('messages.lbl_vendor') }}"
            }] : []),
           
            {
                data: 'lab_name',
                name: 'lab_name',
                title: "{{__('messages.lab')}}"
            },
            {
                data: 'contact_number',
                name: 'contact_number',
                title: "{{__('messages.lbl_contact_number')}}"
            },
            {
                data: 'is_available',
                name: 'is_available',
                title: "{{__('messages.lbl_Curent_status')}}",
                width: '5%',
            },
           
            {
                data: 'status',
                name: 'status',
                title: "{{__('messages.lbl_status')}}",
                width: '5%',
            },
            

        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{__('messages.lbl_action')}}",
            width: '5%',
            visible: {!! auth()->user()->can('edit_collector') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            toggleResetButton();
            let approval_status =
            '{{ request()->route('approval_status') ?? '' }}'; 

            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}' + '?approval_status=' +
                approval_status, 
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    let filters = {
                        collector_name: $('#collector_name').val(),
                        vendor_id: '{{ $vendor_id }}',
                        vendor_name: $('#filter_vendor').val(),
                        lab_name: $('#lab_name').val(),
                        country_id: $('#column_country').val(),
                        state_id: $('#column_state').val(),
                        city_id: $('#column_city').val(),
                        gender: $('input[name="gender"]:checked').val(),
                    };

                    toggleResetButton();
                    return filters;
                }
            });
        })

        $('#reset-filter').on('click', function(e) {
            $('#collector_name').val('');
            $('#filter_vendor').val('');
            $('#filter_country, #filter_state, #filter_city').val('').trigger('change');
            $('#lab_name').val('');
            $('input[name="gender"]').prop('checked', false); 
            window.renderedDataTable.ajax.reload(null, false)
            toggleResetButton();
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

      

        $(document).on('change', '#column_country', function() {
           var country = $(this).val();
            $('#column_state').empty();
            $('#column_city').empty();
            stateName(country);
        })


        $('input[name="gender"]').on('change', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });
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

     function toggleResetButton() {
    let filtersApplied =
        $('#collector_name').val() ||
        $('#filter_vendor').val() ||
        $('#lab_name').val() ||
        $('#column_country').val() ||
        $('#column_state').val() ||
        $('#column_city').val() ||
        $('input[name="gender"]:checked').val();

    if (filtersApplied) {
        $('#reset-filter').show(); // Show button if any filter is applied
    } else {
        $('#reset-filter').hide(); // Hide button if no filters are applied
    }
}

    </script>
@endpush
