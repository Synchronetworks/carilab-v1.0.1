<div class="d-flex gap-3 align-items-center justify-content-end">
    @hasPermission('edit_currency')
        <!-- Edit button -->
        <a class="text-success fs-4" href="{{ route('backend.currencies.edit', $data->id) }}">
            <i class="ph ph-pencil-simple-line align-middle"></i>
        </a>
    @endhasPermission

    @if(!$data->trashed())
        @hasPermission('delete_currency')
        <!-- Soft Delete (Trash) -->
        <a class="text-danger fs-4 delete-tax" href="{{ route('backend.currencies.destroy', $data->id) }}">
            <i class="ph ph-trash align-middle"></i>
        </a>
        @endhasPermission
    @else
        <!-- Restore link -->
        @hasPermission('restore_currency')
        <a class="text-info fs-4" href="{{ route('backend.currencies.restore', $data->id) }}">
            <i class="ph ph-arrow-clockwise align-middle"></i>
        </a>
        @endhasPermission  
        @hasPermission('force_delete_currency')
        <!-- Force Delete link -->
        <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.currencies.force_delete', $data->id) }}">
            <i class="ph ph-trash align-middle"></i>
        </a>
        @endhasPermission 
    @endif
</div>
