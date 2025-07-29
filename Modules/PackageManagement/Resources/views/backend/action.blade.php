<div class="d-flex gap-3 align-items-center justify-content-end">

  @hasPermission('delete_packages')
  @if(!$data->trashed())
  @hasPermission('edit_packages')
  <a  class="text-success fs-4" href="{{ route('backend.packagemanagements.edit', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
  @endhasPermission
  <!-- Soft Delete (Trash) -->
  <a class="text-danger fs-4 delete-tax" href="{{ route('backend.packagemanagements.destroy', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@else
  <!-- Restore link -->
  <a class="text-info fs-4 restore-tax" href="{{ route('backend.packagemanagements.restore', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}">
      <i class="ph ph-arrow-clockwise align-middle"></i>
  </a>
  <!-- Force Delete link -->
  <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.packagemanagements.force_delete', $data->id) }}"  data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@endif
  @endhasPermission
</div>

