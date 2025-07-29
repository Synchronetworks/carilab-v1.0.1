<div class="d-flex gap-3 align-items-center justify-content-end">

    @hasPermission('delete_banners')
        @if (!$data->trashed())
            @hasPermission('edit_banners')
                <a class="text-success fs-4" href="{{ route('backend.banners.edit', $data->id) }}" data-bs-toggle="tooltip"
                    title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
            @endhasPermission
            <!-- Soft Delete (Trash) -->
            <a href="{{ route('backend.banners.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
                class="text-danger fs-4" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
                data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
                data-confirm="{{ __('messages.are_you_sure?') }}"data-success-message="{{ __('messages.banner_delete') }}">

                <i class="ph ph-trash align-middle"></i>
            </a>
        @else
            <!-- Restore link -->
            <a class="text-info fs-4 restore-tax" href="{{ route('backend.banners.restore', $data->id) }}"
                data-bs-toggle="tooltip"
                title="{{ __('messages.restore') }}"data-confirm-message="{{ __('messages.are_you_sure_restore') }}"
                data-success-message="{{ __('messages.restore_form') }}">
                <i class="ph ph-arrow-clockwise align-middle"></i>
            </a>

            <a href="{{ route('backend.banners.force_delete', $data->id) }}"
                id="delete-{{ $module_name }}-{{ $data->id }}" class="text-danger fs-4" data-type="ajax"
                data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip"
                title="{{ __('messages.force_delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
                <i class="ph ph-trash align-middle"></i>
            </a>
        @endif
    @endhasPermission
</div>
