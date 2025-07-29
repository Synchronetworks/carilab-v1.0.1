@extends('backend.layouts.app')
@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="form-content">
        <form action="{{ route('backend.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
            @csrf
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="card-input-title">
                    <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
                </div>
                <a href="{{ route('backend.categories.index') }}" class="btn btn-sm btn-primary">
                    <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
                </a>
            </div>
            <div class="card">
                <div class="card-body">                    
                    @method('PUT')
                    {{ html()->hidden('id',$category->id ?? null) }}
                    <!-- Basic Information -->
                    <div class="row gy-4">                            
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.category_image') }}</label>
                                <div class="btn-file-upload">
                                    <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                        <img id="imagePreview" src="{{ $category->getCategoryImageAttribute() ?? setBaseUrlWithFileName() }}" 
                                            alt="Category Image" class="img-thumbnail avatar-150 object-cover">
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center text-center gap-3">
                                        <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                                            {{ __('messages.upload_image') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" id="removeButton" style="{{ $category->image ? '' : 'display: none;' }}">
                                            {{ __('messages.remove_image') }}
                                        </button>
                                    </div>
                                </div>
                                <input type="file" name="category_image" id="categoryImageInput" class="form-control d-none" accept="image/*">
                                @error('category_image') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ __('messages.category_name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" 
                                        value="{{ old('name', $category->name) }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.category_name_required') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">{{ __('messages.lbl_status') }}</label>
                                    <div class="d-flex align-items-center justify-content-between form-control">
                                        <label for="status" class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="status" value="0">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" 
                                                value="1" {{ old('status', $category->status) == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="description" class="form-label">{{ __('messages.lbl_description') }}</label>
                                    <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $category->description) }}</textarea>
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
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
    const $uploadButton = $('#uploadButton');
    const $removeButton = $('#removeButton');
    const $categoryImageInput = $('#categoryImageInput');
    const $imagePreview = $('#imagePreview');

    // Upload Button Click
    $uploadButton.on('click', function() {
        $categoryImageInput.trigger('click');
    });

    // Image Preview
    $categoryImageInput.on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $imagePreview.attr('src', e.target.result);
                $imagePreview.show();
                $removeButton.show();
            }
            reader.readAsDataURL(file);
        }
    });

    // Remove Button Click
    $removeButton.on('click', function() {
        $categoryImageInput.val('');
        $imagePreview.attr('src', '{{ asset("images/default-image.png") }}');
        $(this).hide();
    });
        tinymce.init({
            selector: '#description',
            plugins: 'link image code',
            toolbar: 'undo redo | styleselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | removeformat | code | image',
            setup: function(editor) {
                // Setup TinyMCE to listen for changes
                editor.on('change', function(e) {
                    // Get the editor content
                    const content = editor.getContent().trim();
                    const $textarea = $('#description');
                    const $error = $('#desc-error');
        
                    // Check if content is empty
                    if (content === '') {
                        $textarea.addClass('is-invalid'); // Add invalid class if empty
                        $error.show(); // Show validation message
                    } else {
                        $textarea.removeClass('is-invalid'); // Remove invalid class if not empty
                        $error.hide(); // Hide validation message
                    }
                });
            }
        });
    });
</script>
@endpush
