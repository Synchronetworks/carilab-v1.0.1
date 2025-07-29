@extends('backend.layouts.app')

@section('title', $module_title)



@section('content')
    <div class="table-content">
        <x-backend.section-header>
          
                <div class="d-flex flex-wrap gap-3">
                @if ($type == null)
                    @if (auth()->user()->can('edit_user') ||
                        auth()->user()->can('delete_user'))
                    <x-backend.quick-action url="{{ route('backend.' . $module_name . '.bulk_action') }}">
                        <div class="">
                            <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                @can('edit_user')
                                <option value="change-status">{{ __('messages.lbl_status') }}</option>
                                @endcan
                                @can('delete_user')
                                <option value="delete">{{ __('messages.delete') }}</option>
                                @endcan
                                @can('restore_user')
                                <option value="restore">{{ __('messages.restore') }}</option>
                                @endcan
                                @can('force_delete_user')
                                  <option value="permanently-delete">{{ __('messages.permanent_dlt') }}</option>
                                @endcan
                                </select>
                        </div>
                        <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="form-select select2" id="status" style="width:100%">
                                {{--     --}}
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
                    @endif
                    @if ($type == 'soon-to-expire')
                    <button id="send-email-btn" class="btn btn-primary">{{ __('messages.send_reminder') }}</button>
                @endif
                </div>
           
            <x-slot name="toolbar">
               
                @if ($type == null)
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
                @if ($type == null)
                    <a href="{{ route('backend.' . $module_name . '.create') }}"
                        class="btn btn-primary d-flex justify-content-center align-items-center gap-1" id="add-post-button"> <i
                            class="ph ph-plus-circle"></i>{{ __('messages.new') }}</a>
                @endif
                <button class="btn btn-secondary d-flex justify-content-center align-items-center gap-1 btn-group" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i class="ph ph-funnel"></i>{{__('messages.advance_filter')}}</button>

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

  <x-backend.advance-filter>
            <x-slot name="title">
                <h4 class="mb-0">{{ __('messages.lbl_advanced_filter') }}</h4>
            </x-slot>
            <form action="javascript:void(0)" class="datatable-filter">
                <div class="row gy-4">
                    <div class="form-group datatable-filter">
                        <label class="form-label" for="user_name">
                            @if($type === 'soon-to-expire')
                                {{ __('messages.vendors') }}
                            @else
                                {{ __('messages.user') }}
                            @endif
                        </label>
                        <select id="user_name" name="user_name" data-filter="select" class="select2 form-control"
                                data-ajax--url="{{ route('backend.get_search_data', [
                                    'type' => $type === 'soon-to-expire' ? 'vendor' : 'customers'
                                ]) }}" 
                                data-ajax--cache="true" data-placeholder="{{ $type === 'soon-to-expire' ? __('messages.select_vendor') : __('messages.select_user') }}">
                        </select>
                    </div>
                    {{-- User Type Filter --}}
                    @if ($type != 'soon-to-expire')
                    <div class="form-group">
                        <label class="form-label" for="user_type">{{ __('messages.lbl_user_type') }}</label>
                        <select name="user_type" id="user_type" class="form-select select2" data-filter="select">
                            <option value="">{{ __('messages.all') }}</option>
                        
                            <option value="user">{{ __('messages.customer') }}</option>
                            <option value="vendor">{{ __('messages.vendor') }}</option>
                            <option value="collector">{{ __('messages.collector') }}</option>
                        </select>
                    </div>
                    @endif

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
                visible: {!! auth()->user()->can('edit_user') || auth()->user()->can('delete_user') ? 'true' : 'false' !!},
                render: function(data, type, row) {
                    return `<input type="checkbox" class="form-check-input user-checkbox" name="user_checkbox" value="${row.id}">`;
                }

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
                title: "{{ $type === 'soon-to-expire' ? __('messages.vendor') : __('messages.lbl_user') }}"
            },
         
             {
                 data: 'mobile',
                 name: 'mobile',
                 title: "{{ __('messages.lbl_contact_number') }}"
             },
             @if ($type != 'soon-to-expire')
            {
                data: 'user_type',
                name: 'user_type',
                title: "{{ __('messages.lbl_user_type') }}",
                orderable: true,
                searchable: true
            },
            @endif
             {

                 data: 'gender',
                 name: 'gender',
                 title: "{{ __('messages.lbl_gender') }}"
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
            visible: {!! auth()->user()->can('edit_user') || auth()->user()->can('delete_user') ? 'true' : 'false' !!}

        }]

        let finalColumns = [
            ...columns,
        ]
        if (!('{{ $type }}')) {
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
                url: '{{ route('backend.users.index_data', ['type' => $type]) }}',
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    return {
                        name: $('#user_name').val(),
                        user_type: $('#user_type').val(),
                        gender: $('input[name="gender"]:checked').val(),
                  
                    }
                }

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
      
        $('#reset-filter').on('click', function(e) {
        $('#user_name').val('');
        $('#user_type').val('').trigger('change');
        $('input[name="gender"]').prop('checked', false); 
            window.renderedDataTable.ajax.reload(null, false);
        });
        $('#quick-action-type').change(function() {
            resetQuickAction()
        });

        $('input[name="gender"]').on('change', function() {
            window.renderedDataTable.ajax.reload(null, false);
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
                let selectedUserIds = [];
                $('input[name="user_checkbox"]:checked').each(function() {
                    selectedUserIds.push($(this).val());
                });

                if (selectedUserIds.length === 0) {
                    Swal.fire("{{ __('messages.warning') }}", "{{ __('messages.no_user_selected') }}", "warning");
                    return;
                }
                
                $.ajax({
                    url: '{{ route('backend.send.email') }}',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        user_ids: selectedUserIds,
                    },
                    success: function(response) {
                        showMessage(response.message);
                    },
                    error: function(xhr, status, error) {
                        console.error("{{ __('messages.failed_to_send_emails') }}" + error);
                    }
                });
            }

            // Change checkbox event handler to use .on()
            $('#select-all-table').on('click', function() {
                $('input[name="user_checkbox"]').prop('checked', this.checked);
            });
        });
        $(document).ready(function() {
        function checkFilters() {
            let userName = $('#user_name').val();
            let userType = $('#user_type').val();
            let gender = $('input[name="gender"]:checked').val();

            if (userName || userType || gender) {
                $('#reset-filter').show();
            } else {
                $('#reset-filter').hide();
            }
        }

        checkFilters();

    $('#user_name, #user_type, input[name="gender"]').on('change', function() {
        checkFilters();
    });

    $('#reset-filter').on('click', function() {
        $('#user_name').val('');
        $('#user_type').val('').trigger('change');
        $('input[name="gender"]').prop('checked', false);

        checkFilters(); 
        window.renderedDataTable.ajax.reload(null, false);
    });
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
                confirmButtonText: "{{ __('messages.yes_delete_it') }}"
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
