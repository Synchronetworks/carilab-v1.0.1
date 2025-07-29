<div class="d-flex gap-3 align-items-center justify-content-end">
  @if(!$data->trashed())
  @hasPermission('edit_commisions')
       <a  class="text-success fs-4" href="{{ route('backend.commisions.edit', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>

  @endhasPermission
  
  <!-- Soft Delete (Trash) -->
  @hasPermission('delete_commisions')

  <a href="{{ route('backend.commisions.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}" class="text-danger fs-4" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
    <i class="ph ph-trash align-middle"></i>
</a>
  @endhasPermission
@else
  <!-- Restore link -->
  @haspermission('restore_commisions')
  <a class="text-primary fs-4 restore-tax" data-bs-toggle="tooltip" title="{{__('messages.restore')}}" href="{{ route('backend.commisions.restore', $data->id) }}" data-confirm-message="{{__('messages.are_you_sure_restore')}}" 
   data-success-message="{{__('messages.restore_form')}}">
      <i class="ph ph-arrow-clockwise align-middle"></i>
  </a>
  @endhasPermission

  @haspermission('force_delete_commisions')
  <!-- Force Delete link -->
  <a href="{{ route('backend.commisions.force_delete', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}" class="text-danger fs-4 force-delete-tax" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.force_delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
            <i class="ph ph-trash align-middle"></i>
        </a>
      @endhasPermission
@endif

</div>
