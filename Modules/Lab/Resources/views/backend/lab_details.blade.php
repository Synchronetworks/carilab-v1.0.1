@if($data !== null)
<a href="{{route('backend.labs.details',$data->id)}}">
  <div class="d-flex gap-3 align-items-center">
      <img src="{{  $data->getLogoUrlAttribute() != null ? $data->getLogoUrlAttribute() : setBaseUrlWithFileName() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
      <div class="text-start">
        <h6 class="m-0">{{ $data->name ?? default_user_name() }}</h6>
        <span>{{ $data->email ?? '--' }}</span>
      </div>
  </div>
</a>
@else
  <span>--</span>
@endif

