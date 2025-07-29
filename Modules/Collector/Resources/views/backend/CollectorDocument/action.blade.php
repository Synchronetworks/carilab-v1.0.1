<div class="d-flex gap-3 align-items-center justify-content-end">
    @if (!$data->trashed())
        @hasPermission('edit_collectordocuments')
            <a class="text-success fs-4" href="{{ route('backend.collectordocument.create', ['id' => $data->id ?? ' ']) }}"
                data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i
                    class="ph ph-pencil-simple-line align-middle"></i></a>
        @endhasPermission

        @hasPermission('delete_collectordocuments')
            <a href="{{ route('backend.collectordocument.destroy', $data->id) }}"
                id="delete-collectordocument-{{ $data->id }}" class="text-danger fs-4 delete-tax" data-type="ajax"
                data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip"
                title="{{ __('messages.delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
                <i class="ph ph-trash align-middle"></i>
            </a>
        @endhasPermission
    @else
        @haspermission('restore_collectordocuments')
            <a class="text-primary fs-4 restore-tax" data-bs-toggle="tooltip" title="{{ __('messages.restore') }}"
                href="{{ route('backend.collectordocument.restore', $data->id) }}"
                data-confirm-message="{{ __('messages.are_you_sure_restore') }}"
                data-success-message="{{ __('messages.restore_form') }}">
                <i class="ph ph-arrow-clockwise align-middle"></i>
            </a>
        @endhasPermission

        @haspermission('force_delete_collectordocuments')
            <a href="{{ route('backend.collectordocument.force_delete', $data->id) }}"
                id="delete-{{ $module_name }}-{{ $data->id }}" class="text-danger fs-4 force-delete-tax"
                data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip"
                title="{{ __('messages.force_delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
                <i class="ph ph-trash align-middle"></i>
            </a>
        @endhasPermission
    @endif

</div>
