<div class="d-flex gap-3 align-items-center justify-content-end">


  @hasPermission('delete_lab')
  @if(!$data->trashed())
  @hasPermission('edit_lab')

  <!-- Edit Lab Session -->
  <a  class="text-primary fs-4" href="{{ route('backend.labsession.edit', $data->id) }}" data-bs-toggle="tooltip" data-bs-original-title="{{__('messages.session')}}"> <i class="ph ph-clock-user align-middle"></i></a>
  
  <!-- Edit Lab -->
       <a  class="text-success fs-4" href="{{ route('backend.labs.edit', $data->id) }}" data-bs-toggle="tooltip" data-bs-original-title="{{__('messages.edit')}}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
       <a class="text-info fs-4" href="{{ route('backend.labs.details', $data->id) }}"
        data-bs-toggle="tooltip" title="{{ __('messages.details') }}">
        <i class="ph ph-eye align-middle"></i>
    </a>
  @endhasPermission
  <!-- Soft Delete (Trash) -->
  <a class="text-danger fs-4 delete-tax" href="{{ route('backend.labs.destroy', $data->id) }}"  data-bs-toggle="tooltip" data-bs-original-title="{{__('messages.delete')}}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@else
  <!-- Restore link -->
  <a class="text-info fs-4 restore-tax" href="{{ route('backend.labs.restore', $data->id) }}"  data-bs-toggle="tooltip" data-bs-original-title="{{__('messages.restore')}}">
      <i class="ph ph-arrow-clockwise align-middle"></i>
  </a>
  <!-- Force Delete link -->
  <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.labs.force_delete', $data->id) }}"  data-bs-toggle="tooltip" data-bs-original-title="{{__('messages.delete')}}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@endif
  @endhasPermission
</div>

