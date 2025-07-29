<div class="d-flex gap-3 align-items-center justify-content-end">

    @hasPermission('delete_catelog')
        @if (!$data->trashed())
            @hasPermission('edit_catelog')
                <a class="text-success fs-4" href="{{ route('backend.catlogmanagements.edit', $data->id) }}"
                    data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i
                        class="ph ph-pencil-simple-line align-middle"></i></a>
            @endhasPermission
            <!-- Soft Delete (Trash) -->
            <a class="text-danger fs-4 delete-tax" href="{{ route('backend.catlogmanagements.destroy', $data->id) }}"
                data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
                <i class="ph ph-trash align-middle"></i>
            </a>
        @else
            @hasPermission('restore_catelog')
                <!-- Restore link -->
                <a class="text-info fs-4 restore-tax" href="{{ route('backend.catlogmanagements.restore', $data->id) }}"
                    data-bs-toggle="tooltip" title="{{ __('messages.restore') }}">
                    <i class="ph ph-arrow-clockwise align-middle"></i>
                </a>
            @endhasPermission
            <!-- Force Delete link -->
            @hasPermission('force_delete_catelog')
                <a class="text-danger fs-4 force-delete-tax"
                    href="{{ route('backend.catlogmanagements.force_delete', $data->id) }}" data-bs-toggle="tooltip"
                    title="{{ __('messages.force_delete') }}">
                    <i class="ph ph-trash align-middle"></i>
                </a>
            @endhasPermission
        @endif
    @endhasPermission
</div>
