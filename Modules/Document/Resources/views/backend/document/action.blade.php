<div class="d-flex gap-3 align-items-center justify-content-end">

  @hasPermission('delete_documents')
    @if(!$data->trashed())
    @hasPermission('edit_document')
    <a  class="text-success fs-4" data-bs-toggle="tooltip" title="{{__('messages.edit')}}" href="{{ route('backend.documents.edit', $data->id) }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
    @endhasPermission
    <!-- Soft Delete (Trash) -->
    <a class="text-danger fs-4 delete-tax" href="{{ route('backend.documents.destroy', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
        <i class="ph ph-trash align-middle"></i>
    </a>
    @else
      <!-- Restore link -->
      <a class="text-info fs-4 restore-tax" href="{{ route('backend.documents.restore', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}">
          <i class="ph ph-arrow-clockwise align-middle"></i>
      </a>
      <!-- Force Delete link -->
      <a class="text-danger force-delete-tax" href="{{ route('backend.documents.force_delete', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}">
          <i class="ph ph-trash align-middle"></i>
      </a>
    @endif
  @endhasPermission
</div>

