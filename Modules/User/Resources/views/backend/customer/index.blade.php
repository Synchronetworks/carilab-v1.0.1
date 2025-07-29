@extends('backend.layouts.app')

@section('title', __('messages.customers'))


@section('content')
    <div class="table-content">
        <x-backend.section-header>
            @if ($type == null || $type  == 'customer')
                <div class="d-flex flex-wrap gap-3">
                    @if (auth()->user()->can('edit_customer') ||
                        auth()->user()->can('delete_customer'))
                    <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('edit_customer')
                                <option value="change-status">{{ __('messages.lbl_status') }}</option>
                                @endcan
                                @can('delete_customer')
                                <option value="delete">{{ __('messages.delete') }}</option>
                                @endcan
                                @can('restore_customer')
                                <option value="restore">{{ __('messages.restore') }}</option>
                                @endcan 
                                @can('force_delete_customer')
                                 <option value="permanently-delete">{{ __('messages.permanent_dlt') }}</option>
                                 @endcan
                                </select>
                        </div>
                    
                        <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="form-select select2" id="status" style="width:100%">
                                <option value="" selected>{{ __('messages.select_status') }}</option>
                                <option value="1">{{ __('messages.active') }}</option>
                                <option value="0">{{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                    </x-backend.quick-action>

                    @endif
                    <div>
                        <button type="button" class="btn btn-secondary flex-lg-grow-0 flex-grow-1" data-modal="export">
                            <i class="ph ph-export align-middle"></i> {{ __('messages.export') }}
                        </button>

                    </div>
                </div>
            @endif
            <x-slot name="toolbar">
                @if ($type == 'soon-to-expire')
                    <button id="send-email-btn" class="btn btn-primary">{{ __('messages.send_reminder') }}</button>
                @endif
                @if ($type == null|| $type  == 'customer')
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
                @endif
                <div class="input-group flex-nowrap">
                    <span class="input-group-text pe-0" id="addon-wrapping"><i
                            class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                        aria-label="Search" aria-describedby="addon-wrapping">
                </div>
                @if ($type == null|| $type  == 'customer')
                    <a href="{{ route('backend.' . 'customer' . '.create') }}"
                        class="btn btn-primary d-flex align-items-center gap-1" id="add-post-button"> <i
                            class="ph ph-plus-circle"></i>{{ __('messages.new') }}</a>
                @endif

            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
    </div>
    <!-- Success Message Container -->
    <div id="success-message" class="alert alert-success"
        style="display: none; text-align: center; width: auto; position: fixed; top: 0; right: 0; margin: 50px;">
        <strong>{{ __('messages.mail_success') }}</strong> {{ __('messages.mail_send') }}
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
                visible: {!! auth()->user()->can('edit_customer') || auth()->user()->can('delete_customer') ? 'true' : 'false' !!}

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
                title: "{{ __('messages.lbl_customer') }}"
            },
            {
                data: 'mobile',
                name: 'mobile',
                title: "{{ __('messages.lbl_contact_number') }}"
            },


            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "{{ __('messages.lbl_status') }}"
            },
            

        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.action') }}",
            width: '5%',
            visible: {!! auth()->user()->can('edit_customer') || auth()->user()->can('delete_customer') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
        ]
        

        if (!('{{ $type }}')) {
        finalColumns = [...finalColumns, ...actionColumn];
    }
    if (('{{ $type }}'=='customer')) {
        finalColumns = [...finalColumns, ...actionColumn];
    }

    if (('{{ $type }}' == 'soon-to-expire')) {
      finalColumns.push({
          name: 'expire_date',
          data: 'expire_date',
          title: "{{ __('messages.end_date') }}",
          orderable: false,
          searchable: false,
      });
    }


        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.users.index_data",['type' => $type]) }}',
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],

            });
           
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
            resetActionButtons()
        })



        function showMessage(message) {
            Snackbar.show({
                text: message,
                pos: 'bottom-left'
            });
        }
        $(document).ready(function() {
    $('#send-email-btn').on('click', function() {
        const confirmationMessage = "{{ __('messages.sure_to_send_email') }}";
        confirmSwal(confirmationMessage).then((result) => {
            if (result.isConfirmed) {
                sendEmail();
            }
        });
    });

    function sendEmail() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '{{ route('backend.send.email') }}',
            type: 'POST',
            data: {
                _token: csrfToken
            },
            success: function(response) {
                showMessage(response.message);
            },
            error: function(xhr, status, error) {
                console.error("{{ __('messages.failed_to_send_emails') }}" + error);
            }
        });
    }
});
    </script>


    <script>
        function tableReload() {
            $('#datatable').DataTable().ajax.reload();
        }
        $(document).on('click', '[data-form-delete]', function() {
            const URL = $(this).attr('data-form-delete')
            Swal.fire({
                title: "{{ __('messages.are_you_sure') }}",
                text: "{{ __('messages.You_wont_be_able_to_revert_this') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "{{ __('messages.Yes_delete_it') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: URL,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(res) {
                            Swal.fire({
                                title: "{{ __('messages.deleted') }}",
                                text: "{{ __('messages.deleted_successfully') }}",
                                icon: "success"
                            });
                            tableReload()
                        }
                    })
                }
            });
        });
    </script>
@endpush
