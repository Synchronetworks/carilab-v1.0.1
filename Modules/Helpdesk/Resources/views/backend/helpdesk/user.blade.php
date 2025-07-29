<a href="{{route('backend.users.details',($query->users)->id)}}">
  <div class="d-flex gap-3 align-items-center">
    <img src="{{ optional($query->users)->profile_image }}" alt="avatar" class="avatar avatar-40 rounded-pill">
    <div class="text-start">
      <h6 class="m-0">{{ optional($query->users)->full_name ?? default_user_name() }}</h6>
      <span>{{ optional($query->users)->email ?? '--' }}</span>
    </div>
  </div>
</a>
