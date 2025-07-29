<div class="d-flex gap-3 align-items-center justify-content-end">
    @if (!$data->trashed())
        <a class="text-primary fs-4" href="{{ route('backend.categories.edit', $data->id) }}" data-bs-toggle="tooltip"
            title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>

        <a class="text-danger delete-tax fs-4" href="{{ route('backend.categories.destroy', $data->id) }}"
            data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?') }}">
            <i class="ph ph-trash align-middle"></i>
        </a>
    @else
        <a class="text-success restore-tax fs-4" href="{{ route('backend.categories.restore', $data->id) }}"
            data-bs-toggle="tooltip" title="{{ __('messages.restore') }}">
            <i class="ph ph-arrow-clockwise align-middle"></i>
        </a>

        <a class="text-danger force-delete-tax fs-4" href="{{ route('backend.categories.force_delete', $data->id) }}"
            data-bs-toggle="tooltip" title="{{ __('messages.force_delete') }}">
            <i class="ph ph-trash align-middle"></i>
        </a>
    @endif
</div>
