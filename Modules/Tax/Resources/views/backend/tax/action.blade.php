<div class="d-flex gap-3 align-items-center justify-content-end">
  @if(!$data->trashed())
    @hasPermission('edit_taxes')
        <a  class="text-success fs-4" href="{{ route('backend.taxes.edit', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
    @endhasPermission
  
    <!-- Soft Delete (Trash) -->
    @hasPermission('delete_taxes')

      <a href="{{ route('backend.taxes.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}" class="text-danger fs-4" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
          <i class="ph ph-trash align-middle"></i>
      </a>
    @endhasPermission
  @else
  <!-- Restore link -->
    @haspermission('restore_taxes')
    <a class="text-primary fs-4 restore-tax" data-bs-toggle="tooltip" title="{{__('messages.restore')}}" href="{{ route('backend.taxes.restore', $data->id) }}" data-confirm-message="{{__('messages.are_you_sure_restore')}}" 
    data-success-message="{{__('messages.restore_form')}}">
        <i class="ph ph-arrow-clockwise align-middle"></i>
    </a>
    @endhasPermission

    @haspermission('force_delete_taxes')
    <!-- Force Delete link -->
    <a href="{{ route('backend.taxes.force_delete', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}" class="text-danger fs-4" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.force_delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
        <i class="ph ph-trash align-middle"></i>
    </a>
    @endhasPermission
@endif

</div>

