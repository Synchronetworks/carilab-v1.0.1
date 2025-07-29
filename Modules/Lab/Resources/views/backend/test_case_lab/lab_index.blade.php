@extends('backend.layouts.app')

@section('title') {{__($module_title)}} @endsection


@section('content')
<div class="table-content">

    <x-backend.section-header>
      
        <x-slot name="toolbar">
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
    <input type="hidden" id="lab_ids" value='@json($labIds)'>
</div>

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
            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            width: '0%',
            exportable: false,
            orderable: false,
            searchable: false,
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
        {
            data: 'price',
            name: 'price',
            title: "{{ __('messages.lbl_price') }}",
        },

       

        ]


        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('messages.lbl_action') }}",
            width: '5%'
        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]


        document.addEventListener('DOMContentLoaded', (event) => {
            let labIds = JSON.parse(document.getElementById('lab_ids').value || '[]');
            initDatatable({
                url: '{{ route("backend.labs.lab_index_data") }}',
                
                finalColumns,
                orderColumn: [
                    [1, "desc"]
                ],
                advanceFilter: () => {
                    return {
                        lab_ids: labIds,
                        test_case_id: '{{ $testcaseid }}',
                        test_package_id: '{{ $testpackageid }}'
                }
            }
                });
            })
       
    </script>
@endpush