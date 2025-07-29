@if ($data->status == 1)
    <div class="d-flex gap-3 align-items-center justify-content-end">

        @hasPermission('delete_collector')
            @if (!$data->trashed())
                @hasPermission('edit_collector')
                    <a class="text-success fs-4" href="{{ route('backend.collectors.edit', $data->id) }}" data-bs-toggle="tooltip"
                        title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
                    <a class="text-primary fs-4" href="{{ route('backend.collectors.details', $data->id) }}"
                        data-bs-toggle="tooltip" title="{{ __('messages.details') }}"> <i class="ph ph-eye align-middle"></i></a>
                    @if ($data->login_type != 'google')
                        <a class="text-warning fs-4" data-bs-toggle="tooltip" title="{{ __('messages.change_password') }}"
                            href="{{ route('backend.collectors.changepassword', $data->id) }}">
                            <i class="ph ph-lock align-middle"></i>
                        </a>
                    @endif
                @endhasPermission

                <a class="text-danger fs-4 delete-tax" href="{{ route('backend.collectors.destroy', $data->id) }}"
                    data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
                    <i class="ph ph-trash align-middle"></i>
                </a>
            @else
                <a class="text-info fs-4 restore-tax" href="{{ route('backend.collectors.restore', $data->id) }}"
                    data-bs-toggle="tooltip" title="{{ __('messages.restore') }}">
                    <i class="ph ph-arrow-clockwise align-middle"></i>
                </a>

                <a class="text-danger fs-4 force-delete-tax"
                    href="{{ route('backend.collectors.force_delete', $data->id) }}" data-bs-toggle="tooltip"
                    title="{{ __('messages.force_delete') }}">
                    <i class="ph ph-trash align-middle"></i>
                </a>
            @endif
        @endhasPermission
    </div>
@elseif($data->status == 0)
    <a class="approve" href="{{ route('backend.approve', ['type' => 'collector', 'id' => $data->id]) }}"
        data-bs-toggle="tooltip" title="{{ __('messages.approve') }}"><i class="ph ph-check"></i></a>


    <a href="{{ route('backend.collectors.force_delete', $data->id) }}"
        id="delete-{{ $module_name }}-{{ $data->id }}" class="text-danger fs-4" data-type="ajax"
        data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip"
        title="{{ __('messages.reject') }}" data-confirm="{{ __('messages.are_you_sure_reject') }}">
        <i class="ph ph-x"></i>
    </a>
@endif
