<div class="d-flex gap-3 align-items-center justify-content-end">
  @hasPermission('edit_constants')
       <a  class="text-success fs-4" href="{{ route('backend.constants.edit', $data->id) }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>

  @endhasPermission
  @hasPermission('delete_constants')
  @if(!$data->trashed())
  <!-- Soft Delete (Trash) -->
  <a class="text-danger fs-4 delete-tax" href="{{ route('backend.constants.destroy', $data->id) }}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@else
  <!-- Restore link -->
  <a class="text-info fs-4" href="{{ route('backend.constants.restore', $data->id) }}">
      <i class="ph ph-arrow-clockwise align-middle"></i>
  </a>
  <!-- Force Delete link -->
  <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.constants.force_delete', $data->id) }}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@endif
  @endhasPermission
</div>

