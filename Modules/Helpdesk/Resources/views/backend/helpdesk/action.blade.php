<div class="d-flex gap-3 align-items-center justify-content-end">
  <a class="text-info fs-4" href="{{ route('backend.helpdesks.show',$data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.view') }}"><i class="ph ph-eye"></i></a>

  @if($data->status == 0)
<a class="text-success fs-4" href="{{ route('backend.helpdesks.closed',$data->id) }}" data-bs-toggle="tooltip" title="{{ __('messages.marked_closed') }}"><i class="ph ph-check"></i></a>
@endif

</div>

