@extends('backend.layouts.app')
@section('title') {{ __($module_action) }} {{__($module_title)}}  @endsection
@section('content')
<div class="form-content">
    <form action="{{ route('backend.banners.store') }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
    @csrf
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
            </div>
            <a href="{{ route('backend.banners.index') }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="form-group col-md-4">
                        <label class="form-label">{{ __('messages.image') }}</label>
                        <div class="btn-file-upload">
                            <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                <img id="imagePreview"
                                    src="{{ $data->profile_image ?? default_placeholder() }}"
                                    alt="Profile Image" class="img-thumbnail avatar-150 object-cover">
                            </div>
                            <div class="d-flex justify-content-center align-items-center text-center gap-3">
                                <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                                    {{ __('messages.upload_image') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" id="removeButton" style="display: none;">
                                    {{ __('messages.remove_image') }}
                                </button>
                            </div>
                        </div>
                        <input type="file" name="banner_image" id="imageInput" class="form-control d-none" accept="image/*">
                        <small id="imageError" class="text-danger" style="display: none;">{{ __('messages.invalid_image_format') }}</small>
                        @error('banner_image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-8 mt-md-0 mt-3">
                        <div class="row gy-4">
                            <div class="col-lg-4 col-md-12">
                                <label for="name" class="form-label">{{ __('messages.banner_name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="{{ __('messages.placeholder_banner_name') }}" id="name" 
                                    value="{{ old('name') }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                <div class="invalid-feedback">{{ __('messages.banner_name_required') }}</div>
                            </div>
                            
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('messages.banner_type') }}<span class="text-danger">*</span></label>
                                <select class="form-select select2" name="banner_type" id="banner_type" required>
                                    <option value="" disabled selected>{{ __('messages.select_banner_type') }}</option>
                                    <option value="test_case">{{ __('messages.lbl_test_case') }}</option>
                                    <option value="test_package">{{ __('messages.test_package') }}</option>
                                </select>
                                @error('banner_type') <span class="text-danger">{{ $message }}</span> @enderror
                                <div class="invalid-feedback">{{ __('messages.test_type_required') }}</div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label for="status" class="form-label">{{ __('messages.status') }}</label>
                                <div class="d-flex align-items-center justify-content-between form-control">
                                    <label for="status" class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" 
                                            value="1" {{ old('status', 1) == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                                <textarea class="form-control" name="description" id="description" placeholder="{{ __('messages.placeholder_description') }}" rows="3">{{ old('description') }}</textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                {{__('messages.save')}}
            </button>
        </div>
    </form>
</div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function() {
        // Image Handling
        const uploadButton = document.getElementById('uploadButton');
        const removeButton = document.getElementById('removeButton');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');

        uploadButton.on('click', function() {
            imageInput.trigger('click');
        });

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validImageTypes.includes(file.type)) {
                    imageError.style.display = 'block';
                    imageInput.value = '';
                    imagePreview.src = '{{ asset("default-image/Default-Image.jpg") }}';
                    removeButton.style.display = 'none';
                } else {
                    imageError.style.display = 'none';
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        removeButton.style.display = 'inline-block';
                    }
                    reader.readAsDataURL(file);
                }
            }
        });

        removeButton.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.src = '{{ asset("images/default-image.png") }}';
            this.style.display = 'none';
        });

    });
</script>
@endpush
