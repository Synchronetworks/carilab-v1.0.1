@if(auth()->user()->can('delete_reviews'))
@if(!$data->trashed()) 
        <!-- Soft Delete (Trash) -->
        <a class="text-danger fs-4 delete-tax" href="{{ route('backend.reviews.destroy', $data->id) }}">
            <i class="ph ph-trash align-middle"></i>
        </a>
      @else
      <!-- Restore link -->
      <a class="text-info fs-4 restore-tax" href="{{ route('backend.reviews.restore', $data->id) }}">
          <i class="ph ph-arrow-clockwise align-middle"></i>
      </a>
      <!-- Force Delete link -->
      <a class="text-danger fs-4 force-delete-tax" href="{{ route('backend.reviews.force_delete', $data->id) }}">
          <i class="ph ph-trash align-middle"></i>
      </a>
    @endif
@endif

