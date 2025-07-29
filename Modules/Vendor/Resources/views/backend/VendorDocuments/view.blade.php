
@extends('backend.layouts.app')

@section('title') {{__($module_title)}} @endsection
@section('content')


  <div class="table-content">
      <x-backend.section-header>
          <div class="d-flex flex-wrap gap-3">
              @if(auth()->user()->can('edit_vendordocuments') || auth()->user()->can('delete_vendordocuments'))
              <x-backend.quick-action url="{{ route('backend.vendordocument.bulk-action') }}">
                  <div class="">
                      <select name="action_type" class="form-select select2 col-12" id="quick-action-type"
                          style="width:100%">
                          <option value="">{{ __('messages.no_action') }}</option>
                          @can('edit_vendordocuments')
                          <option value="change-status">{{ __('messages.lbl_status') }}</option>
                          @endcan
                          @can('delete_vendordocuments')
                          <option value="delete">{{ __('messages.delete') }}</option>
                          <option value="restore">{{ __('messages.restore') }}</option>
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

            
              <div class="input-group flex-nowrap">
                  <span class="input-group-text pe-0" id="addon-wrapping"><i
                          class="ph ph-magnifying-glass"></i></span>
                  <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... " aria-label="Search"
                      aria-describedby="addon-wrapping">
              </div>
                @hasPermission('add_vendordocuments')
                <a href="{{ route('backend.vendordocument.create',['vendordocument' => $vendordata->id??' ']) }}" class=" btn btn-primary d-flex align-items-center gap-1"><i class="ph ph-plus-circle align-middle"></i>{{ __('messages.new') }}</a>
                @endhasPermission
          
          </x-slot>
      </x-backend.section-header>
      <table id="datatable" class="table table-responsive">
      </table>
  </div>




 
  </div>
  @if (session('success'))
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
    

      const columns= [{
            name: 'check',
            data: 'check',
            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="vendordocument" onclick="selectAllTable(this)">',
            exportable: false,
            orderable: false,
            searchable: false,
            visible: {!! auth()->user()->can('edit_vendordocuments') || auth()->user()->can('delete_vendordocuments') ? 'true' : 'false' !!}

          },
          {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('product.lbl_update_at') }}",
            orderable: true,
            visible: false,
          },
          {
            data: 'vendor_id',
            name: 'vendor_id',
            title: "{{ __('messages.vendor') }}",
            orderable: false,
          },
          {
            data: 'document_id',
            name: 'document_id',
            title: "{{ __('messages.document') }}"
          },
          {
            data: 'is_verified',
            name: 'is_verified',
            title: "{{ __('messages.is_verified') }}"
          },
          {
            data: 'status',
            name: 'status',
            title: "{{ __('messages.status') }}"
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.action') }}",
            className: 'text-end',
            visible: {!! auth()->user()->can('edit_vendordocuments') || auth()->user()->can('delete_vendordocuments') ? 'true' : 'false' !!}

          }

        ]


        let finalColumns = [
            ...columns,
            
        ]
      document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.vendordocument.index_data", ["vendordocument" => $vendordata->id??'null']) }}',
                finalColumns,
                orderColumn: [  [1, 'desc']]
                
            });
        })

   
    function resetQuickAction() {
      const actionValue = $('#quick-action-type').val();
   
      if (actionValue != '') {
        $('#quick-action-apply').removeAttr('disabled');

        if (actionValue == 'change-featured') {
          $('.quick-action-featured').addClass('d-none');
          $('#change-featured-action').removeClass('d-none');
        } else {
          $('.quick-action-featured').addClass('d-none');
        }
      } else {
        $('#quick-action-apply').attr('disabled', true);
        $('.quick-action-field').addClass('d-none');
        $('.quick-action-featured').addClass('d-none');
      }
    }

    $('#quick-action-type').change(function() {
      resetQuickAction()
    });

    $(document).on('update_quick_action', function() {

    })

    $(document).on('click', '[data-ajax="true"]', function(e) {
    e.preventDefault();
    const button = $(this);
    const confirmation = button.data('confirmation');

    if (confirmation === 'true') {
        const message = button.data('message');
        if (confirm(message)) {
            const submitUrl = button.data('submit');
            const $form = button.closest('form');
            $form.attr('action', submitUrl);
            
            $form.on('submit', function(e) {
                e.preventDefault();
                $(this).trigger('submit');
            }).trigger('submit');
        }
    } else {
        const submitUrl = button.data('submit');
        const $form = button.closest('form');
        $form.attr('action', submitUrl);
        
        $form.on('submit', function(e) {
            e.preventDefault();
            $(this).trigger('submit');
        }).trigger('submit');
    }
});
  </script>
<link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">
<script src="{{ asset('js/sweetalert2.js') }}"></script>
@endpush