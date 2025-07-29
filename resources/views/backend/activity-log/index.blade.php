@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

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
                    <span class="input-group-text pe-0" id="addon-wrapping">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}... "
                        aria-label="Search" aria-describedby="addon-wrapping">
                </div>
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive"></table>
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
                data: 'id',
                name: 'id',
                title: "{{ __('messages.id') }}",
                orderable: true,
            },
            {
                data: 'created_by',
                name: 'created_by',
                title: "{{ __('messages.created_by') }}",
                orderable: true,
            },
            {
                data: 'log_name',
                name: 'log_name',
                title: "{{ __('messages.log_name') }}",
                orderable: true,
            },
            {
                data: 'subject_type',
                name: 'subject_type',
                title: "{{ __('messages.table_name') }}",
                orderable: true,
            },
            {
                data: 'description',
                name: 'description',
                title: "{{ __('messages.activity_description') }}"
            },
            {
                data: 'created_at',
                name: 'created_at',
                title: "{{ __('messages.created_at') }}",
                orderable: true,
            },
            
        ];

        const finalColumns = [...columns];

        document.addEventListener('DOMContentLoaded', (event) => {
            const userType = '{{ $user_type ?? '' }}';
            initDatatable({
                url: '{{ route("backend.activity-log.index_data") }}',
                finalColumns,
                orderColumn: [[1, "desc"]],
                advanceFilter: () => {
                    return {
                        user_type: userType
                    }
                },
                search: {
                    selector: '.dt-search',
                    smart: true,
                    user_type: userType
                }
            });
        });
    </script>
@endpush
