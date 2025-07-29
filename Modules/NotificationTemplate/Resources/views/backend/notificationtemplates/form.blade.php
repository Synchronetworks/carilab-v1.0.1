<div class="container-fluid">
    <div class="row">
        <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
        <div class="card">
            <div class="card-body">

                {{ html()->hidden('id', $data->id ?? null) }}
                {{ html()->hidden('type', $data->type ?? null) }}

                <div class="row gy-4">
                    <div class="form-group col-md-3">
                        <label class="form-label">{{ __('messages.type') }} : <span class="text-danger">*</span></label>
                        <select name="type" class="select2 form-control" id="type"
                            data-ajax--url="{{ route('backend.notificationtemplates.ajax-list', ['type' => 'constants_key', 'data_type' => 'notification_type']) }}"
                            data-ajax--cache="true" required disabled>
                            @if (isset($data->type))
                                <option value="{{ $data->type }}" selected>{{ $data->constant->name ?? '' }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">{{ __('messages.to') }} :</label><br>
                        <select name="to[]" id="toSelect" class="select2 form-control"
                            data-ajax--url="{{ route('backend.notificationtemplates.ajax-list', ['type' => 'constants_key', 'data_type' => 'notification_to']) }}"
                            data-ajax--cache="true" multiple>
                            @if (isset($data) && $data->to != null)
                                @foreach (json_decode($data->to) as $to)
                                    <option value="{{ $to }}" selected="">
                                        {{ $to === 'user' ? 'customer' : $to }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-3">

                        @php
                            $toValues = json_decode($data->to, true) ?? [];
                        @endphp
                        {{ html()->label(__('messages.user_type') . ': <span class="text-danger">*</span>', 'user_type')->class('form-label') }}
                        {{ html()->select('defaultNotificationTemplateMap[user_type]', $toValues, null)->class('form-select select2')->id('userTypeSelect')->required() }}
                    </div>
                    <div class="form-group col-md-3">
                        {{ html()->label(trans('messages.status') . ':', 'status')->class('form-label') }}
                        {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $data->status)->id('role')->class('form-select select2')->required() }}
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">{{ __('messages.parameters') }} :</label><br>
                            <div class="main_form">
                                @if (isset($buttonTypes))
                                    @include(
                                        'notificationtemplate::backend.notificationtemplates.perameters-buttons',
                                        ['buttonTypes' => $buttonTypes]
                                    )
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-5">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <h4>{{ __('messages.notification_template') }}</h4>
                                </div>

                                <div class="form-group">
                                    <label class="form-label float-start">{{ __('messages.subject') }} :</label>
                                    <input type="text" name="defaultNotificationTemplateMap[subject]" value=""
                                        class="form-control">
                                    <input type="hidden" name="defaultNotificationTemplateMap[status]" value="1"
                                        class="form-control">
                                </div>

                                <div class="text-left mt-4">
                                    <label class="form-label">{{ __('messages.template') }} :</label>
                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                </div>
                                <div class="form-group">
                                    {{ html()->textarea('defaultNotificationTemplateMap[template_detail]')->class('form-control textarea tinymce-template')->id('notification_mytextarea') }}
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">

                                    <h4>{{ __('messages.mail_template') }}</h4>

                                </div>

                                <div class="form-group">
                                    <label class="form-label float-start">{{ __('messages.subject') }} :</label>
                                    {{ html()->text('defaultNotificationTemplateMap[mail_subject]', $data->defaultNotificationTemplateMap['mail_subject'] ?? '')->class('form-control') }}
                                    {{ html()->hidden('defaultNotificationTemplateMap[status]', 1)->class('form-control') }}
                                </div>

                                <div class="text-left mt-4">
                                    <label class="form-label">{{ __('messages.template') }} :</label>
                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                </div>

                                <div class="form-group">
                                    {{ html()->textarea('defaultNotificationTemplateMap[mail_template_detail]')->class('form-control textarea tinymce-template')->id('mail_mytextarea') }}
                                </div>

                            </div>
                            <!-- whatsapp and sms -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">

                                    <h4>{{ __('messages.sms_template') }}</h4>

                                </div>

                                <div class="form-group">
                                    <label class="form-label float-start">{{ __('messages.subject') }} :</label>
                                    {{ html()->text('defaultNotificationTemplateMap[sms_subject]', $data->defaultNotificationTemplateMap['sms_subject'] ?? '')->class('form-control') }}
                                    {{ html()->hidden('defaultNotificationTemplateMap[status]', 1)->class('form-control') }}
                                </div>

                                <div class="text-left mt-4">
                                    <label class="form-label">{{ __('messages.template') }} :</label>
                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                </div>

                                <div class="form-group">
                                    {{ html()->textarea('defaultNotificationTemplateMap[sms_template_detail]')->class('form-control textarea tinymce-template')->id('sms_mytextarea') }}
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">

                                    <h4>{{ __('messages.whatsapp_template') }}</h4>

                                </div>

                                <div class="form-group">
                                    <label class="form-label float-start">{{ __('messages.subject') }} :</label>
                                    {{ html()->text('defaultNotificationTemplateMap[whatsapp_subject]', $data->defaultNotificationTemplateMap['whatsapp_subject'] ?? '')->class('form-control') }}
                                    {{ html()->hidden('defaultNotificationTemplateMap[status]', 1)->class('form-control') }}
                                </div>

                                <div class="text-left mt-4">
                                    <label class="form-label">{{ __('messages.template') }} :</label>
                                    {{ html()->hidden('defaultNotificationTemplateMap[language]', 'en') }}
                                </div>

                                <div class="form-group">
                                    {{ html()->textarea('defaultNotificationTemplateMap[whatsapp_template_detail]')->class('form-control textarea tinymce-template')->id('whatsapp_mytextarea') }}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary"> {{ __('messages.save') }}<i
                            class="md md-lock-open"></i></button>

                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
    <script src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('vendor/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
    <script type="text/javascript">
        if (typeof(tinyMCE) != "undefined") {
            // tinymceEditor()
            function tinymceEditor(target, button, callback, height = 200) {
                var rtl = $("html[lang=ar]").attr('dir');
                tinymce.init({
                    selector: target || '.textarea',
                    directionality: rtl,
                    height: height,

                    relative_urls: false,
                    remove_script_host: false,
                    content_css: [],
                    image_advtab: true,
                    menubar: false,
                    plugins: [
                        "textcolor colorpicker image imagetools media charmap link print textcolor code codesample table"
                    ],
                    toolbar: "image undo redo | link image | code table",
                    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist | removeformat | code | image |' +
                        button,
                    image_title: true,
                    automatic_uploads: true,
                    setup: callback,
                    convert_urls: false,
                    file_picker_types: 'image',
                    file_picker_callback: function(cb, value, meta) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');

                        input.onchange = function() {
                            var file = this.files[0];
                            var reader = new FileReader();
                            reader.onload = function() {
                                var id = 'blobid' + (new Date()).getTime();
                                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                var base64 = reader.result.split(',')[1];
                                var blobInfo = blobCache.create(id, file, base64);
                                blobCache.add(blobInfo);

                                cb(blobInfo.blobUri(), {
                                    title: file.name
                                });
                            };
                            reader.readAsDataURL(file);
                        };

                        $(input).trigger('click');
                    }
                });
            }
        }
        (function($) {
            $(document).ready(function() {
                tinymceEditor('.tinymce-template', ' ', function(ed) {

                }, 450)

            });

        })(jQuery);

        $(document).ready(function() {
            $('.select2-tag').select2({
                tags: true,
                createTag: function(params) {
                    if (params.term.length > 2) {
                        return {
                            id: params.term,
                            text: params.term,
                            newTag: true
                        }
                    }
                    return null;
                }
            });
        });

        function onChangeType(url, render) {
            var dropdown = document.getElementById("type");
            var selectedValue = dropdown.value;
            var url = "{{ route('backend.notificationtemplates.notification-buttons', ['type' => 'buttonTypes']) }}";
            $.get(url, function(data) {
                var html = data;
                if (render !== undefined && render !== '' && render !== null) {
                    $('.' + render).html(html);
                } else {
                    $(".main_form").html(html);
                    $("#formModal").modal("show");
                }
            });
        }

        $(document).ready(function() {
            $('.select2').select2();

            $('select[name="defaultNotificationTemplateMap[user_type]"]').off('change').on('change', function() {
                var userType = $(this).val();
                var type = $('select[name="type"]').val();
                $.ajax({
                    url: "{{ route('backend.notificationtemplates.fetchnotification_data') }}",
                    method: "GET",
                    data: {
                        user_type: userType,
                        type: type
                    },
                    success: function(response) {

                        if (response.success) {
                            var data = response.data
                            var notification_template_data = response.notification_template_data

                            if (data) {

                                $("input[name='defaultNotificationTemplateMap[subject]']").val(
                                    data.subject);
                                $("textarea[name='defaultNotificationTemplateMap[template_detail]']")
                                    .val(data.template_detail);

                            } else {

                                $("input[name='defaultNotificationTemplateMap[subject]']").val(
                                    '');
                                $("textarea[name='defaultNotificationTemplateMap[template_detail]']")
                                    .val('');
                                tinymce.get('notification_mytextarea').setContent('');

                            }

                            if (data) {

                                $("input[name='defaultNotificationTemplateMap[mail_subject]']")
                                    .val(data.mail_subject);
                                $("textarea[name='defaultNotificationTemplateMap[mail_template_detail]']")
                                    .val(data.mail_template_detail);

                            } else {

                                $("input[name='defaultNotificationTemplateMap[mail_subject]']")
                                    .val('');
                                $("textarea[name='defaultNotificationTemplateMap[mail_template_detail]']")
                                    .val('');
                                tinymce.get('mail_mytextarea').setContent('');
                            }

                            if (data) {

                                $("input[name='defaultNotificationTemplateMap[sms_subject]']")
                                    .val(data.sms_subject);
                                $("textarea[name='defaultNotificationTemplateMap[sms_template_detail]']")
                                    .val(data.sms_template_detail);

                            } else {

                                $("input[name='defaultNotificationTemplateMap[sms_subject]']")
                                    .val('');
                                $("textarea[name='defaultNotificationTemplateMap[sms_template_detail]']")
                                    .val('');
                                tinymce.get('sms_mytextarea').setContent('');
                            }

                            if (data) {

                                $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']")
                                    .val(data.whatsapp_subject);
                                $("textarea[name='defaultNotificationTemplateMap[whatsapp_template_detail]']")
                                    .val(data.whatsapp_template_detail);

                            } else {

                                $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']")
                                    .val('');
                                $("textarea[name='defaultNotificationTemplateMap[whatsapp_template_detail]']")
                                    .val('');
                                tinymce.get('whatsapp_mytextarea').setContent('');
                            }


                        } else {
                            $("input[name='defaultNotificationTemplateMap[subject]']").val('');
                            $("textarea[name='defaultNotificationTemplateMap[template_detail]']")
                                .val('');
                            tinymce.get('notification_mytextarea').setContent('');
                            $("input[name='defaultNotificationTemplateMap[mail_subject]']").val(
                                '');
                            $("textarea[name='defaultNotificationTemplateMap[mail_template_detail]']")
                                .val('');
                            tinymce.get('mail_mytextarea').setContent('');
                            $("input[name='defaultNotificationTemplateMap[sms_subject]']").val(
                                '');
                            $("textarea[name='defaultNotificationTemplateMap[sms_template_detail]']")
                                .val('');
                            tinymce.get('sms_mytextarea').setContent('');
                            $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']")
                                .val('');
                            $("textarea[name='defaultNotificationTemplateMap[whatsapp_template_detail]']")
                                .val('');
                            tinymce.get('whatsapp_mytextarea').setContent('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
        $(document).ready(function() {
            var toSelect = $('#toSelect');
            var userTypeSelect = $('#userTypeSelect');

            function updateUserTypeOptions(selectedValues) {
                userTypeSelect.empty();

                if (selectedValues) {
                    selectedValues.forEach(function(value) {
                        userTypeSelect.append(new Option(value, value));
                    });
                }
                userTypeSelect.trigger('change');
            }

            var initialSelectedValues = toSelect.val();
            updateUserTypeOptions(initialSelectedValues);

            toSelect.on('change', function() {
                var selectedValues = $(this).val();
                updateUserTypeOptions(selectedValues);
            });

            toSelect.select2();
            userTypeSelect.select2();
        });
        $(document).on('click', '#variable_button', function() {

            const textarea = $(document).find('.tab-pane.active');
            const textareaID = textarea.find('textarea').attr('id');
            const valueToInsert = $(this).attr('data-value');

            if (textareaID) {
                const editor = tinymce.get(textareaID);
                if (editor) {
                    editor.selection.setContent(valueToInsert);
                }
            }
        });
    </script>
@endpush
