@extends('backend.layouts.app')
@section('title') {{ __($module_action) }} {{__($module_title)}}  @endsection

@section('content')
<div class="form-content">
    <form action="{{ route('backend.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
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
                @method('PUT')
                {{ html()->hidden('id',$banner->id ?? null) }}
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="form-group col-md-4">
                        <label class="form-label">{{ __('messages.image') }}</label>
                        <div class="btn-file-upload">
                            <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                <img id="imagePreview" src="{{ $banner->getbannerImageAttribute() ?? setBaseUrlWithFileName() }}" 
                                    alt="{{ __('messages.image') }}" class="img-thumbnail avatar-150 object-cover">
                            </div>
                            <div class="d-flex justify-content-center align-items-center text-center gap-3">
                                <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                                    {{ __('messages.upload_image') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" id="removeButton" style="{{ $banner->image ? '' : 'display: none;' }}">
                                    {{ __('messages.remove_image') }}
                                </button>
                            </div>
                        </div>
                        <input type="file" name="banner_image" id="BannerImageInput" class="form-control d-none" accept="image/*">
                        @error('banner_image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-8">
                        <div class="row gy-4">
                            <div class="col-lg-4 col-md-12">
                                <label for="name" class="form-label">{{ __('messages.banner_name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('messages.placeholder_banner_name') }}"
                                    value="{{ old('name', $banner->name) }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                <div class="invalid-feedback">{{ __('messages.banner_name_required') }}</div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('messages.banner_type') }}<span class="text-danger">*</span></label>
                                <select class="form-select select2" name="banner_type" id="banner_type" required>
                                    <option value="" disabled selected>{{ __('messages.select_banner_type') }}</option>
                                    <option value="test_case" {{ old('banner_type', $banner->banner_type) == 'test_case' ? 'selected' : '' }}>{{ __('messages.test_case') }}</option>
                                    <option value="test_package" {{ old('banner_type', $banner->banner_type) == 'test_package' ? 'selected' : '' }}>{{ __('messages.test_package') }}</option>
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
                                            value="1" {{ old('status', $banner->status) == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                                <textarea class="form-control" name="description" id="description" placeholder="{{ __('messages.placeholder_description') }}" rows="3">{{ old('description', $banner->description) }}</textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary float-end">
                {{ __('messages.save') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function() {
        // Image Handling
        // Elements
        const uploadButton = document.getElementById('uploadButton');
        const removeButton = document.getElementById('removeButton');
        const bannerImageInput = document.getElementById('bannerImageInput');
        const imagePreview = document.getElementById('imagePreview');

        // Upload Button Click
        uploadButton.on('click', function() {
            imageInput.trigger('click');
        });

        // Image Preview
        bannerImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    removeButton.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove Button Click
        removeButton.addEventListener('click', function() {
            profileImageInput.value = '';
            imagePreview.src = '';
            imagePreview.style.display = 'none';
            this.style.display = 'none';
        });
        
    });
</script>
@endpush
