<div class="d-flex gap-3 align-items-center justify-content-end">
  @if(!$data->trashed())
  @hasPermission('edit_state')
       <a  class="btn btn-primary" href="{{ route('backend.state.edit', $data->id) }}" ata-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>

  @endhasPermission
 
  @hasPermission('delete_state')
  <!-- Soft Delete (Trash) -->
  <a class="mr-3 delete-tax" href="{{ route('backend.state.destroy', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
      <i class="ph ph-trash align-middle"></i>
  </a>
  @endhasPermission
@else
  <!-- Restore link -->
  @haspermission('restore_state')
  <a class="btn btn-info btn-sm fs-4" href="{{ route('backend.state.restore', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}">
      <i class="ph ph-arrow-clockwise align-middle"></i>
  </a>
  @endhasPermission
  @haspermission('force_delete_state')
  <!-- Force Delete link -->
  <a class="force-delete-tax" href="{{ route('backend.state.force_delete', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}">
      <i class="ph ph-trash align-middle"></i>
  </a>
  @endhasPermission
@endif

</div>

