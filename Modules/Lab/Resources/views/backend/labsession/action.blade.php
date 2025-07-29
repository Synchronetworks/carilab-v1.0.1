<div class="d-flex gap-3 align-items-center">
    @hasPermission('edit_lab')
    <a  class="text-success fs-4" href="{{ route('backend.labsession.edit', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
    @endhasPermission
</div>
