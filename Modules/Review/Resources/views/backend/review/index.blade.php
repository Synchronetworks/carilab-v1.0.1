@extends('backend.layouts.app')

@section('title',__('messages.reviews'))


@section('content')
    <div class="table-content">
        
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    @if(auth()->user()->can('edit_reviews') || auth()->user()->can('delete_reviews'))
                    <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('delete_reviews')
                                <option value="delete">{{ __('messages.delete') }}</option>
                                @endcan
                                @can('restore_reviews')
                                <option value="restore">{{ __('messages.restore') }}</option>
                                @endcan
                                @can('force_delete_reviews')
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

                    <div>
                        <div class="datatable-filter">
                            <select name="column_status" id="column_status" class="select2 form-control"
                                data-filter="select" style="width: 100%">
                                <option value="lab">
                                    {{ __('messages.labs') }}</option>
                                <option value="collector" selected>
                                    {{ __('messages.collectors') }}</option>
                            </select>
                        </div>
                    </div>
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
    <x-backend.advance-filter>
        <x-slot name="title">
            <h4 class="mb-0">{{ __('messages.lbl_advanced_filter') }}</h4>
        </x-slot>
        <form action="javascript:void(0)" class="datatable-filter">
            <div class="form-group">
                <label class="form-label" for="user_name"> {{ __('messages.lbl_customer_name') }} </label>
                <select name="filter_service_id" id="user_name" name="user_name" data-filter="select"
                    class="select2 form-control"
                    data-ajax--url="{{ route('backend.get_search_data', ['type' => 'posts']) }}"
                    data-ajax--cache="true">
                </select>
            </div>
        </form>
        <button type="reset" class="btn btn-danger mt-4" id="reset-filter">{{__('messages.reset')}}</button>
    </x-backend.advance-filter>
    @if(session('success'))
        <div class="snackbar" id="snackbar">
            <div class="d-flex justify-content-around align-items-center">
                <p class="mb-0">{{ session('success') }}</p>
                <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">{{__('messages.dismiss')}}</a>
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
        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="reviews" onclick="selectAllTable(this)">',
        width: '0%',
        exportable: false,
        orderable: false,
        searchable: false,
        visible: {!! auth()->user()->can('edit_reviews') || auth()->user()->can('delete_reviews') ? 'true' : 'false' !!}

    },
    {
        data: 'updated_at',
        name: 'updated_at',
        title: "{{ __('messages.lbl_update_at') }}",
        orderable: true,
        visible: false,
        searchable: false,
    },
    {
        data: 'collector',
        name: 'collector',
        title: "{{__('messages.lbl_collector_name')}}",  // Assuming `name` is a field in the `Collector` model
    },
    {
        data: 'lab',
        name: 'lab',
        title: "{{__('messages.labs')}}",  // Assuming `name` is a field in the `Collector` model
    },
    {
        data: 'user',
        name: 'user',  // Assuming `name` is a field in the `User` model
        title: "{{__('messages.lbl_customer_name')}}",
    },
    {
        data: 'rating',
        name: 'rating',
        title: "{{__('messages.lbl_rating')}}",
    },
    {
        data: 'review',
        name: 'review',
        className: "description-column",
        title: "{{__('messages.review')}}",
    },
    
];


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.lbl_action') }}",
            width: '5%',
            visible: {!! auth()->user()->can('edit_reviews') || auth()->user()->can('delete_reviews') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [[ 1, "desc" ]],
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
