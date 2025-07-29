<div class="d-flex gap-3 align-items-center justify-content-end">
    @hasPermission('view_prescription')
        @if($data->prescription_status == 1 && $data->is_notify == 1)
            <form action="{{ route('backend.prescriptions.send_suggestion', $data->id) }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="is_notify" value="1">
                <button type="submit" class="text-primary fs-4 border-0 bg-transparent">
                    <i class="ph ph-bell-simple-ringing align-middle"></i>
                </button>
            </form>
        @endif
    
    
    

    @endhasPermission
    @hasPermission('delete_prescription')
    
      @if(!$data->trashed())
        @if ($data->is_notify == 1)   
             <a  class="text-primary fs-4" href="{{ route('backend.prescriptions.show', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.detail') }}"> <i class="ph ph-eye align-middle"></i></a>  
        @endif
        <!-- Soft Delete (Trash) -->
        <a class="text-danger fs-4 delete-tax" href="{{ route('backend.prescriptions.destroy', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
            <i class="ph ph-trash align-middle"></i>
        </a>
      @else
      <!-- Restore link -->
      <a class="text-info fs-4 restore-tax" href="{{ route('backend.prescriptions.restore', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}">
          <i class="ph ph-arrow-clockwise align-middle"></i>
      </a>
      <!-- Force Delete link -->
      <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.prescriptions.force_delete', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}">
          <i class="ph ph-trash align-middle"></i>
      </a>
      @endif
    @endhasPermission
</div>

