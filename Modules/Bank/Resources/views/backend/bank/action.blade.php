<div class="d-flex gap-3 align-items-center justify-content-end">
@if(auth()->user()->can('delete_collector_bank') || auth()->user()->can('delete_vendor_bank'))
 
  @if(!$data->trashed())
    @if(auth()->user()->can('edit_collector_bank') || auth()->user()->can('edit_vendor_bank'))
        <a  class="text-success fs-4" href="{{ route('backend.banks.edit', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line align-middle"></i></a>
    @endif
    <a class="text-danger fs-4 delete-tax" href="{{ route('backend.banks.destroy', $data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
        <i class="ph ph-trash align-middle"></i>
    </a>
  @else
    <a class="text-info fs-4 restore-tax" href="{{ route('backend.banks.restore', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}">
        <i class="ph ph-arrow-clockwise align-middle"></i>
    </a>
    <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.banks.force_delete', $data->id) }}" data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}">
        <i class="ph ph-trash align-middle"></i>
    </a>
  @endif
@endif

</div>

