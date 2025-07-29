@if($data->status == 1)
<div class="d-flex gap-3 align-items-center justify-content-end">

  @hasPermission('delete_vendor')
    @if(!$data->trashed())
    @hasPermission('edit_vendor')
       <a  class="text-success fs-4" href="{{ route('backend.vendors.edit', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
       <a  class="text-primary fs-4" href="{{ route('backend.vendors.details', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.detail') }}"> <i class="ph ph-eye align-middle"></i></a>
       <a class="text-warning fs-4" data-bs-toggle="tooltip"
           title="{{ __('messages.change_password') }}" href="{{ route('backend.vendors.changepassword', $data->id) }}">
           <i class="ph ph-lock align-middle"></i>
       </a>
  @endhasPermission
      <!-- Soft Delete (Trash) -->
      <a class="text-danger fs-4 delete-tax" href="{{ route('backend.vendors.destroy', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
          <i class="ph ph-trash align-middle"></i>
      </a>
    @else
    <!-- Restore link -->
    <a class="text-success fs-4 restore-tax" href="{{ route('backend.vendors.restore', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}">
        <i class="ph ph-arrow-clockwise align-middle"></i>
    </a>
    <!-- Force Delete link -->
    <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.vendors.force_delete', $data->id) }}"  data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}">
        <i class="ph ph-trash align-middle"></i>
    </a>
@endif
  @endhasPermission
</div>
@elseif($data->status == 0)
<div class="d-flex align-items-center gap-3 justify-content-end">
    <a class="text-success fs-4 approve" href="{{ route('backend.approve', ['type' => 'vendor', 'id' => $data->id]) }}"><i class="ph ph-check"></i></a>
    <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.collectors.force_delete', $data->id) }}">
    <i class="ph ph-x"></i></a>
  </div>
@endif
