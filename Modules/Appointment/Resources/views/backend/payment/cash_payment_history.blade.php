@extends('backend.layouts.app')


@section('title') {{__($module_title)}} {{__($module_action)}} @endsection



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
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... " aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>
                
               
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive appointment-table">
        </table>
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
                title: "{{ __('messages.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
            {
                data: 'sender_id',
                name: 'sender_id',
                title: "{{ __('messages.sender') }}",
                orderable: false,
            },
            {
                data: 'receiver_id',
                name: 'receiver_id',
                title: "{{ __('messages.receiver') }}"
            },
            {
                data: 'datetime',
                name: 'datetime',
                title: "{{ __('messages.datetime') }}"
            },
           
            {
                data: 'text',
                name: 'text',
                title: "{{ __('messages.text') }}"
            },

        ]


        

        let finalColumns = [
            ...columns,
            
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            
            initDatatable({
                url: '{{ route("backend.payments.cash_index_data",['id' => $id]) }}',
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    return {
                       

                    }
                }
            });
        })

        $('#reset-filter').on('click', function(e) {
           
           
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
