<div class="d-flex gap-3 align-items-center justify-content-end">
    @if (optional($data->transactions)->payment_status == 'paid' && $data->test_case_status == 'report_generated')
        <a class="fs-4 text-success upload-btn" href="#" data-bs-toggle="modal"
            data-bs-target="#uploadModal-{{ $data->id }}" data-id="{{ $data->id }}">
            <i class="ph ph-upload-simple align-middle"></i>
        </a>
    @endif

    @hasPermission('delete_bookings')
        @if (!$data->trashed())
            <a class="fs-4 text-primary" data-bs-toggle="tooltip" title="{{ __('messages.detail') }}"
                href="{{ route('backend.appointments.details', $data->id) }}"> <i class="ph ph-eye align-middle"></i></a>
        @endif
        @if (!$data->trashed())
            <a class="fs-4 delete-tax text-danger" data-bs-toggle="tooltip"
                title="{{ __('messages.delete') }}"href="{{ route('backend.appointments.destroy', $data->id) }}">
                <i class="ph ph-trash align-middle"></i>
            </a>
        @else
            <a class="fs-4 text-info restore-tax" href="{{ route('backend.appointments.restore', $data->id) }}">
                <i class="ph ph-arrow-clockwise align-middle"></i>
            </a>

            <a class="fs-4 force-delete-tax text-danger" href="{{ route('backend.appointments.force_delete', $data->id) }}">
                <i class="ph ph-trash align-middle"></i>
            </a>
        @endif
    @endhasPermission
</div>

<div class="modal uploadModal fade" id="uploadModal-{{ $data->id }}" tabindex="-1"
    aria-labelledby="uploadModalLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel-{{ $data->id }}">
                    {{ __('messages.upload_file_appointment') }} #{{ $data->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <div class="mb-3">
                        <label for="fileInput-{{ $data->id }}"
                            class="form-label">{{ __('messages.select_multiple_image_pdfs') }}</label>
                        <input type="file" class="form-control fileInput" id="fileInput-{{ $data->id }}"
                            name="report_generate[]" multiple>
                    </div>
                    <div id="previewContainer-{{ $data->id }}" class="mt-2 row g-3">
                        @foreach ($data->getMedia('report_generate') as $media)
                            @php
                                $name = $media->custom_properties;
                                $originalName = $name['original_name'] ?? 'Unknown File';
                            @endphp

                            @if (str_ends_with($media->mime_type, 'pdf') || str_ends_with($media->mime_type, 'octet-stream'))
                                <div class="col-lg-6">
                                    <div class="position-relative media-container">
                                        <div class="border p-2 rounded d-flex align-items-center gap-2 pdf-preview"
                                            data-url="{{ $media->getUrl() }}" style="cursor: pointer;">
                                            <span>📄</span>
                                            <span class="text-break">{{ $originalName }}</span>
                                        </div>
                                        <button type="button"
                                            class="btn btn-sm btn-danger text-white px-1 remove-media position-absolute top-0 end-0 rounded-circle"
                                            data-id="{{ $media->id }}" style="line-height: 0;"><i
                                                class="ph ph-x align-middle"></i></button>
                                    </div>
                                </div>
                            @else
                                <div class="col-lg-4 col-3">
                                    <div class="position-relative media-container">
                                        <img src="{{ $media->getUrl() }}"
                                            class="preview-image h-100 w-100 object-cover rounded"
                                            style="cursor: pointer;" data-url="{{ $media->getUrl() }}">
                                        <button type="button"
                                            class="btn btn-sm btn-danger text-white px-1 remove-media position-absolute top-0 end-0 rounded-circle"
                                            data-id="{{ $media->id }}" style="line-height: 0;"><i
                                                class="ph ph-x align-middle"></i></button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="button" class="btn btn-primary uploadBtn"
                            data-id="{{ $data->id }}">{{ __('messages.upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade imagePreviewModal" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewLabel">{{ __('messages.image_preview') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalImage" class="img-fluid rounded-5">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on("click", ".preview-image", function() {
            let imageUrl = $(this).data("url");
            $("#modalImage").attr("src", imageUrl);
            $("#imagePreviewModal").modal("show");
        });

        $(document).off("click", ".uploadBtn").on("click", ".uploadBtn", function(e) {
            e.preventDefault();
            let appointmentId = $(this).data("id");
            let form = $("#uploadModal-" + appointmentId).find(".uploadForm")[
            0]; // Get the correct form
            let formData = new FormData(form);
            let csrfToken = $('meta[name="csrf-token"]').attr("content");

            fetch("{{ route('backend.appointments.upload_report') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("{{ __('messages.success') }}",
                            "{{ __('messages.files_uploaded_successfully') }}", "success");


                        bootstrap.Modal.getInstance(document.getElementById("uploadModal-" +
                            appointmentId)).hide();


                        $('#datatable').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire("{{ __('messages.error') }}",
                            "{{ __('messages.error_uploading_files') }}", "error");
                    }
                })
                .catch(error => {
                    console.error("{{ __('messages.upload_error') }}:", error);
                });
        });

        $(document).on("click", ".pdf-preview", function() {
            let pdfUrl = $(this).data("url");
            window.open(pdfUrl, "_blank");
        });

        $(document).on("click", ".remove-media", function() {
            let mediaId = $(this).data("id");
            let mediaElement = $(this).closest(".media-container");
            let csrfToken = $('meta[name="csrf-token"]').attr("content");

            Swal.fire({
                title: "{{ __('messages.confirm_deletion') }}",
                text: "{{ __('messages.are_you_sure_delete') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('messages.yes_delete') }}",
                cancelButtonText: "{{ __('messages.no_cancel') }}",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                allowOutsideClick: false
            }).then((result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: "{{ __('messages.deleting') }}",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch("{{ route('backend.appointments.delete_report') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            id: mediaId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mediaElement.remove();
                            Swal.fire("{{ __('messages.success') }}",
                                "{{ __('messages.file_removed_successfully') }}",
                                "success");

                            $('#datatable').DataTable().ajax.reload(null, false);
                            location.reload();
                        } else {
                            Swal.fire("{{ __('messages.error') }}",
                                "{{ __('messages.error_uploading_files') }}", "error");
                        }
                    })
                    .catch(error => {
                        console.error("{{ __('messages.error') }}", error);
                        let errorTitle = "{{ __('messages.error') }}";
                        let errorMessage = "{{ __('messages.something_went_wrong') }}";

                        Swal.fire(errorTitle, errorMessage, "error");
                    });
            });
        });
    });
</script>
