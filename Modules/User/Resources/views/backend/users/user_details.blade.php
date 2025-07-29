@if($data !== null)
@if($data->status == 1)
<a href="{{ $data->user_type=='collector'?route('backend.collectors.details', $data->id):($data->user_type=='vendor'?route('backend.vendors.details',$data->id):route('backend.users.details', $data->id))}}">
  <div class="d-flex gap-3 align-items-center">
    <img src="{{ isset($data->profile_image) && $data->profile_image ? setBaseUrlWithFileName($data->profile_image) : '#' }}"  alt="avatar" class="avatar avatar-40 rounded-pill">
    <div class="text-start">    
      <h6 class="m-0">{{ $data->full_name ?? default_user_name() }}</h6>
      <span >{{ $data->email ?? '--' }}</span>
    </div>
  </div>
</a>
@elseif(!is_null($data) && isset($data->status) && $data->status == 0)
<div class="d-flex gap-3 align-items-center">
  <img src="{{ isset($data->profile_image) && $data->profile_image ? setBaseUrlWithFileName($data->profile_image) : '#' }}"  alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">    
    <h6 class="m-0">{{ $data->full_name ?? default_user_name() }}</h6>
    <span >{{ $data->email ?? '--' }}</span>
  </div>
</div>
@endif
@else
<span> -- </span>
@endif
