@if($data->submission_status == 'pending' || $data->submission_status == null)
    <select class="form-select suggestion-status-select" data-id="{{ $data->id }}" id="suggestion-status-select-{{ $data->id }}">
        <option value="">{{__('messages.select_action')}}</option>
        <option value="accept">{{__('messages.accept')}}</option>
        <option value="reject">{{__('messages.reject')}}</option>
    </select>
@endif

@if($data->submission_status =='accept' && $data->test_case_status !== 'report_generated')
<div class="test-case-status" id="test-case-status-{{ $data->id }}">
    <select class="form-select test-case-select" data-id="{{ $data->id }}">
        <option value="">{{__('messages.select_test_case_status')}}</option>
        <option value="in_progress" {{ $data->test_case_status == 'in_progress' ? 'selected' : '' }}>{{ __('messages.in_progress') }}</option>
        <option value="awaiting_validation" {{ $data->test_case_status == 'awaiting_validation' ? 'selected' : '' }}>{{ __('messages.awaiting_validation') }}</option>
        <option value="validated" {{ $data->test_case_status == 'validated' ? 'selected' : '' }}>{{ __('messages.validated') }}</option>
        <option value="report_generated" {{ $data->test_case_status == 'report_generated' ? 'selected' : '' }}> {{ __('messages.report_generated') }} </option>
    </select>
</div>
@endif

<script>
    function showMessage(message) {
            Snackbar.show({
                text: message,
                pos: 'bottom-left'
            });
        }

        $(document).ready(function () {
    // Initialize Select2 for better UI
    $('.suggestion-status-select, .test-case-select').select2();

    // Store previous value on focus
    $('.suggestion-status-select, .test-case-select').focus(function () {
        $(this).data('previous-value', $(this).val() || '');
    });

    // Handle status change for accept/reject
    $('.suggestion-status-select').change(function () {
        let $select = $(this);
        let appointmentId = $select.data('id');
        let status = $select.val();
        let statusText = $select.find('option:selected').text();
        let previousValue = $select.data('previous-value');
        
        if (!status) {
            resetSelection($select, previousValue);
            return;
        }

        Swal.fire({
            title: "{{ __('messages.confirm_update') }}",
            text: `{{ __('messages.update_appointment_confirmation', ['status' => ':status']) }}`.replace(':status', statusText.toLowerCase()),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "{{ __('messages.yes_update') }}",
            cancelButtonText: "{{ __('messages.no_cancel') }}"
        }).then((result) => {
            if (!result.isConfirmed) {
                resetSelection($select, previousValue);
                return;
            }

            Swal.fire({
                title: "{{ __('messages.updating') }}",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '{{ route("backend.appointments.acceptRejectAppointment") }}',
                method: 'POST',
                data: {
                    appointment_id: appointmentId,
                    submission_status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    Swal.fire('{{__("messages.success")}}', response.message, 'success');
                    
                    // Update previous value and dropdown
                    $select.data('previous-value', status);
                    $select.val(status).trigger('change.select2');

                    if (status === 'accept') {
                        $('#test-case-status-' + appointmentId).removeClass('d-none');
                    }

                    // Refresh DataTable
                    refreshDataTable();
                },
                error: function (xhr) {
                    handleError(xhr.responseJSON?.message || '{{__("messages.something_went_wrong")}}', $select, previousValue);
                }
            });
        });
    });

    // Handle test-case status change
    $('.test-case-select').change(function () {
        let $select = $(this);
        let appointmentId = $select.data('id');
        let testCaseStatus = $select.val();
        let previousValue = $select.data('previous-value');
        let testCaseStatusText = $select.find('option:selected').text();

        if (!testCaseStatus) {
            resetSelection($select, previousValue);
            return;
        }

        Swal.fire({
            title: "{{ __('messages.confirm_update') }}",
            text: `{{ __('messages.update_test_case_confirmation', ['status' => ':status']) }}`.replace(':status', testCaseStatusText.toLowerCase()),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "{{ __('messages.yes_update') }}",
            cancelButtonText: "{{ __('messages.no_cancel') }}"
        }).then((result) => {
            if (!result.isConfirmed) {
                resetSelection($select, previousValue);
                return;
            }

            Swal.fire({
                title: "{{ __('messages.updating') }}",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '{{ route("backend.appointments.updateTestCaseStatus") }}',
                method: 'POST',
                data: {
                    appointment_id: appointmentId,
                    test_case_status: testCaseStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    Swal.fire('Success!', response.message, 'success');
                    
                    // Update dropdown value
                    $select.val(testCaseStatus).trigger('change.select2');

                    // Update test-case status content
                    $('#test-case-status-' + appointmentId).html(response.updated_html);

                    // Refresh DataTable
                    refreshDataTable();
                },
                error: function (xhr) {
                    handleError(xhr.responseJSON?.message || '{{__("messages.something_went_wrong")}}', $select, previousValue);
                }
            });
        });
    });

    // Function to refresh DataTable
    function refreshDataTable() {
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().ajax.reload(null, false); // Reload data without resetting pagination
        } else if (typeof appointmentTable !== 'undefined') {
            appointmentTable.ajax.reload(null, false);
        }
    }

    function resetSelection($element, previousValue) {
        $element.val(previousValue).trigger('change.select2');
    }

    function handleError(message, $select, previousValue) {
        Swal.fire('Error!', message, 'error').then(() => resetSelection($select, previousValue));
    }
});

</script>
