@if($testcaseid !== null)
<div class="d-flex gap-3 align-items-center justify-content-end">

    <a  class="text-primary fs-4" href="{{ route('backend.catlogmanagements.edit', $testcaseid) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line  align-middle"></i></a>

  </div>
@elseif($testpackageid !== null)
<div class="d-flex gap-3 align-items-center justify-content-end">

    <a  class="text-primary fs-4" href="{{ route('backend.packagemanagements.edit', $testpackageid) }}" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-simple-line  align-middle"></i></a>

  </div>  
  @else
  <span class="text-danger">--</span>
@endif

  