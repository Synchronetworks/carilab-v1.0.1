<div class="form-content mb-0">
    <div class="title mb-5 pb-md-3">
        <h3>{{__('messages.document_verification')}}</h3>
        <p>{{__('messages.explore_feature_create')}}</p>
    </div>
    <form id="step-form" enctype="multipart/form-data">
    @csrf
        <div class="row gy-4">
            @foreach($document as $doc)
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="document_{{ $doc->id }}">
                            {{ $doc->name }} 
                            @if($doc->is_required == 1)
                                <span class="text-danger">*</span> <!-- Required Field -->
                            @endif
                        </label>
                        <input type="file" class="form-control document-input" 
                            id="document_{{ $doc->id }}" 
                            name="documents[{{ $doc->id }}]" 
                            data-required="{{ $doc->is_required }}" 
                            data-id="{{ $doc->id }}">

                        <!-- Hidden field to store document_id -->
                        <input type="hidden" name="document_ids[]" value="{{ $doc->id }}">

                        <!-- Error message container -->
                        <div class="invalid-feedback" id="error-{{ $doc->id }}" style="display: none;"></div>
                        @error('documents[{{ $doc->id }}]') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback ">{{ __('messages.field_required') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 pt-5 d-flex justify-content-end">
            <button type="button" class="btn btn-secondary next-btn">{{ __('messages.next_step') }}</button>
        </div>
    </form>
</div>
