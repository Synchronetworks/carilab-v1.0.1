@extends('backend.layouts.app')

@section('title')
    @if(request()->route('approval_status') === 'pending')
        {{ __('messages.pending_vendor_list') }}
    @else
        {{ __('messages.vendor_list') }}
    @endif
@endsection

@section('content')
    <div class="table-content">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
                @if(auth()->user()->can('edit_vendor') || auth()->user()->can('delete_vendor'))
                <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                    <div class="">
                        <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                            style="width:100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            @can('edit_vendor')
                            <option value="change-status">{{ __('messages.lbl_status') }}</option>
                            @endcan
                            @can('edit_vendor')
                            <option value="delete">{{ __('messages.delete') }}</option>
                            @endcan
                            @can('restore_vendor')
                            <option value="restore">{{ __('messages.restore') }}</option>
                            @endcan
                            @can('force_delete_vendor')
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
                @if(request()->route('approval_status') != 'pending')
                <div>
                    <div class="datatable-filter">
                        <select name="column_status" id="column_status" class="select2 form-control"
                            data-filter="select" style="width: 100%">
                            <option value="">{{__('messages.all')}}</option>
                            <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                {{ __('messages.inactive') }}</option>
                            <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                {{ __('messages.active') }}</option>
                        </select>
                    </div>
                </div>
                @endif
                <div class="input-group flex-nowrap">
                    <span class="input-group-text pe-0" id="addon-wrapping"><i
                            class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... " aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>
                    @hasPermission('add_vendor')
                    @if(!request()->route("approval_status"))
                        <a href="{{ route('backend.' . $module_name . '.create') }}" class="btn btn-primary d-flex justify-content-center align-items-center gap-1" id="add-post-button"> <i class="ph ph-plus-circle align-middle"></i>{{ __('messages.new') }}</a>
                    @endif
                    @endhasPermission
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
          
            <div class="row gy-4">
                <div class="form-group">
                    <label class="form-label" for="filter_vendor"> {{ __('messages.lbl_vendorName') }} </label>
                    <select name="filter_vendor" id="filter_vendor" data-filter="select" class="form-select select2" 
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'vendor']) }}" 
                            data-ajax--cache="true" data-placeholder="{{ __('messages.select_vendor') }}">
                        <option value="">{{ __('messages.select_vendor') }}</option>
                    </select>
                </div>       
                <div class="form-group datatable-filter">
                    <label class="form-label" for="lab_name">{{ __('messages.lbl_lab_name') }}</label>
                    <select id="lab_name" name="lab_name" data-filter="select" class="select2 form-control"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'lab_name']) }}" 
                            data-ajax--cache="true" data-placeholder="{{ __('messages.select_lab') }}">
                        <option value="">{{ __('messages.select_lab') }}</option>
                    </select>
                </div>
        
              
                <div class="form-group">
                    <label class="form-label" for="commission_name"> {{ __('messages.commissions') }} </label>
                    <select name="filter_commision_id" id="commission_name" data-filter="select"
                        class="select2 form-control"
                        data-ajax--url="{{ route('backend.get_search_data', ['type' => 'commission','role' => 'vendor']) }}"
                        data-ajax--cache="true" data-placeholder="{{ __('messages.select_commission') }}">
                        <option value="">{{ __('messages.select_commission') }}</option>
                    </select>
                </div>      
                {{-- Taxes Filter --}}
                <div class="form-group">
                    <label class="form-label" for="filter_tax">{{ __('messages.lbl_taxes') }}</label>
                    <select name="filter_tax" id="filter_tax" data-filter="select" class="form-select select2"
                            data-ajax--url="{{ route('backend.get_search_data', ['type' => 'tax']) }}" 
                            data-ajax--cache="true" data-placeholder="{{ __('messages.select_tax') }}">
                        <option value="">{{ __('messages.select_tax') }}</option>
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
    @if(session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">{{ __('messages.dismiss') }}</a>
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
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="users" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
                visible: {!! auth()->user()->can('edit_vendor') || auth()->user()->can('delete_vendor') ? 'true' : 'false' !!}


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
                title: "{{__('messages.lbl_vendor')}}"
            },
            {
                data: 'contact_number',
                name: 'contact_number',
                title: "{{__('messages.lbl_contact_number')}}"
            },

            {
                data: 'labs_count',
                name: 'labs_count',
                title: "{{__('messages.labs')}}",
                orderable: false,
            },
            {
                data: 'collectors_count',
                name: 'collectors_count',
                title: "{{__('messages.collectors')}}",
                orderable: false,
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
            visible: {!! auth()->user()->can('edit_vendor') || auth()->user()->can('delete_vendor') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            let approval_status = '{{ request()->route("approval_status") ?? "" }}'; // Get 'status' from the route parameter

            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}' + '?approval_status=' + approval_status, // Append the status parameter
                finalColumns,
                 orderColumn: [[ 1, "desc" ]],
                advanceFilter: () => {
                    return {
                        name: $('#user_name').val(),
                        vendor_id: $('#filter_vendor').val(),
                        lab_id: $('#lab_name').val(),
                        collector_id: $('#collector_name').val(),
                        commission_id: $('#commission_name').val(),
                        tax_id: $('#filter_tax').val(),
                        gender: $('input[name="gender"]:checked').val(),
                        status: $('#column_status').val()
                    }
                }
            });
            toggleResetButton();
        })

        function toggleResetButton() {
    let hasFilter = 
        $('#user_name').val() ||
        $('#filter_vendor').val() ||
        $('#lab_name').val() ||
        $('#collector_name').val() ||
        $('#commission_name').val() ||
        $('#filter_tax').val() ||
        $('#column_status').val() ||
        $('input[name="gender"]:checked').length > 0;

    if (hasFilter) {
        $('#reset-filter').show(); // Show Reset button
    } else {
        $('#reset-filter').hide(); // Hide Reset button
    }
}
$('.datatable-filter select, .datatable-filter input[type="radio"]').on('change', function () {
    toggleResetButton(); // Update Reset button visibility
    window.renderedDataTable.ajax.reload(null, false);
});

        $('input[name="gender"]').on('change', function() {
            window.renderedDataTable.ajax.reload(null, false);
        });
        $('#reset-filter').on('click', function(e) {
          
            $('#user_name').val('').trigger('change');
            $('#filter_vendor').val('').trigger('change');
            $('#lab_name').val('').trigger('change');
            $('#collector_name').val('').trigger('change');
            $('#commission_name').val('').trigger('change');
            $('#filter_tax').val('').trigger('change');
            $('#column_status').val('').trigger('change');
    
            window.renderedDataTable.ajax.reload(null, false)
            toggleResetButton(); 
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

      $(document).on('update_quick_action', function() {
        
      })
</script>
@endpush
