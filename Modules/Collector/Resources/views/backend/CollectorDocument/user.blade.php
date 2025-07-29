
@if(isset($query->collectors))

 <div class="d-flex gap-3 align-items-center">
    <img src="{{ getSingleMedia(optional($query->collectors),'profile_image', null) }}" alt="avatar" class="avatar avatar-40 rounded-pill">
    <div class="text-start">
      <h6 class="m-0 tn-link btn-link-hover">{{ optional($query->collectors)->full_name }} </h6>
      <span class="btn-link btn-link-hover">{{ optional($query->collectors)->email ?? '--' }}</span>
    </div>
  </div>


  @else
  <div class="align-items-center">
    <h6 class="text-center">{{ '-' }} </h6>
</div>
  @endif




