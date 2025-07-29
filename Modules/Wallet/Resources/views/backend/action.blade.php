<div class="d-flex gap-3 align-items-center justify-content-end">
  @hasPermission('edit_wallets')
       <a  class="btn btn-primary" href="{{ route('backend.wallets.edit', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>

  @endhasPermission
  @hasPermission('delete_wallets')
  @if(!$data->trashed())
  <!-- Soft Delete (Trash) -->
  <a class="mr-3 delete-tax" href="{{ route('backend.wallets.destroy', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@else
  <!-- Restore link -->
  <a class="btn btn-info btn-sm fs-4" href="{{ route('backend.wallets.restore', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}">
      <i class="ph ph-arrow-clockwise align-middle"></i>
  </a>
  <!-- Force Delete link -->
  <a class="force-delete-tax" href="{{ route('backend.wallets.force_delete', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}">
      <i class="ph ph-trash align-middle"></i>
  </a>
@endif
  @endhasPermission
</div>

