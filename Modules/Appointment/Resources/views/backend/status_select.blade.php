@if($data->status !== 'completed' && $data->status !== 'cancelled')  
<select class="form-select select2 status-select" 
    data-appointment-id="{{ $data->id }}" 
    data-current-status="{{ $data->status }}"
    style="width: 100%">
    <option value="">{{__('messages.select_status')}}</option>
    @foreach($status as $status)
        <option value="{{ $status->value }}" {{ $data->status == $status->value ? 'selected' : '' }}>
            {{ $status->label }}
        </option>
    @endforeach
</select>
@elseif($data->status == 'completed')
<select class="form-select select2 paymentstatus-select" 
    data-appointment-id="{{ $data->id }}"
    data-collector-id="{{ optional($data->appointmentCollectorMapping)->collector_id }}"
    data-current-payment-status="{{ optional($data->transactions)->payment_status }}">
    <option value="">{{__('messages.select_payment_status')}}</option>
    <option value="pending" {{ optional($data->transactions)->payment_status == 'pending' ? 'selected' : '' }}>{{__('messages.pending')}}</option>
    <option value="paid" {{ optional($data->transactions)->payment_status == 'paid' ? 'selected' : '' }}>{{__('messages.paid')}}</option>
    <option value="failed" {{ optional($data->transactions)->payment_status == 'failed' ? 'selected' : '' }}>{{__('messages.failed')}}</option>
</select>
@endif
<div class="modal fade" id="cancellationReasonModal" tabindex="-1" aria-labelledby="cancellationReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancellationReasonModalLabel">{{__('messages.cancellation_reason')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="cancellation_reason" rows="3" placeholder="{{__('messages.enter_cancellation_reason')}}"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.close')}}</button>
                <button type="button" class="btn btn-danger" id="confirmCancellation">{{__('messages.confirm_cancellation')}}</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    // Initialize Select2
    $('.status-select, .paymentstatus-select').select2();

    // Store previous value on focus
    $('.status-select, .paymentstatus-select').focus(function () {
        $(this).data('previous-value', $(this).val() || '');
    });

    // Handle status update
    $('.status-select').change(function () {
        var $select = $(this);
        var statusval = $select.val();

        if (statusval == 'cancelled') {
            $('#cancellationReasonModal').modal('show');   
            $('#confirmCancellation').one('click', function () {
                var cancellationReason = $('#cancellation_reason').val();
                if (cancellationReason) {
                    handleStatusUpdate($select, '{{ url('api/appointment-update') }}', cancellationReason);
                    $('#cancellationReasonModal').modal('hide');
                } else {
                    Swal.fire("{{ __('messages.error') }}", "{{__('messages.cancellation_reasons')}}", 'error');
                }
            });
        } else {
            handleStatusUpdate($select, '{{ url('api/appointment-update') }}');
        }
    });

    // Handle payment status update
    $('.paymentstatus-select').change(function () {
        var $select = $(this);
        var collectorId = $select.data('collector-id');
        var currentUserId = '{{ auth()->id() }}';

        // Check if user is authorized to change payment status
        if (collectorId && collectorId != currentUserId && !'{!! auth()->user()->hasRole("admin") !!}') {
            Swal.fire({
                title: "{{ __('messages.unauthorized') }}",
                text: "{{ __('messages.only_assigned_collector_or_admin_can_update_payment_status') }}",
                icon: 'error'
            });
            resetSelection($select, $select.data('current-payment-status'));
            return;
        }

        handlePaymentStatusUpdate($select);
    });

    function handleStatusUpdate($select, apiUrl, cancellationReason = null) {
        var appointmentId = $select.data('appointment-id');
        var statusval = $select.val();
        var status = $select.find('option:selected').text();
        var previousValue = $select.data('previous-value');
        var payment_type = 'cash';

        // If no value is selected, reset to previous value
        if (!statusval) {
            resetSelection($select, previousValue);
            return;
        }

        Swal.fire({
            title: "{{ __('messages.confirm_update') }}",
            text: `{{ __('messages.update_appointment_confirmation', ['status' => ':status']) }}`.replace(':status', status),
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "{{ __('messages.yes_update') }}",
            cancelButtonText: "{{ __('messages.no_cancel') }}",
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            allowOutsideClick: false
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

            var postData = {
                _token: '{{ csrf_token() }}',
                id: appointmentId,
                status: statusval,
                payment_type: payment_type,
            };

            if (cancellationReason) {
                postData.cancellation_reason = cancellationReason;
            }

            $.ajax({
                url: apiUrl,
                type: 'POST',
                data: postData,
                success: function (response) {
                    if (response.data) {
                        Swal.fire('{{__("messages.success")}}', '{{__("messages.status_updated")}}', 'success')
                            .then(() => {
                                $select.data('previous-value', statusval);
                                refreshDataTable();
                            });
                    } else {
                        handleError(response.message || '{{__("messages.something_went_wrong")}}', $select, previousValue);
                    }
                },
                error: function (xhr) {
                    handleError(xhr.responseJSON?.message || '{{__("messages.something_went_wrong")}}', $select, previousValue);
                }
            });
        });
    }

    function handlePaymentStatusUpdate($select) {
        var appointmentId = $select.data('appointment-id');
        var statusval = $select.val();
        var status = $select.find('option:selected').text();
        var previousValue = $select.data('current-payment-status');
        var payment_type = 'cash';

        if (!statusval) {
            resetSelection($select, previousValue);
            return;
        }

        Swal.fire({
            title: "{{ __('messages.confirm_payment_update') }}",
            text: `{{ __('messages.update_payment_status_confirmation', ['status' => ':status']) }}`.replace(':status', status),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "{{ __('messages.yes_update') }}",
            cancelButtonText: "{{ __('messages.no_cancel') }}"
        }).then((result) => {
            if (!result.isConfirmed) {
                resetSelection($select, previousValue);
                return;
            }

            $.ajax({
                url: '{{ url("api/save-payment") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: appointmentId,
                    payment_status: statusval,
                    payment_type: payment_type
                },
                success: function (response) {
                    if (response.data) {
                        $select.data('current-payment-status', statusval);
                        // Store the current status before refresh
                        var currentStatus = $('.status-select').data('current-status');
                        Swal.fire('{{__("messages.success")}}', '{{__("messages.payment_status_updated")}}', 'success')
                            .then(() => {
                                refreshDataTable();
                                // Ensure status is maintained
                                if (currentStatus === 'completed') {
                                    setTimeout(() => {
                                        $('.status-select').val('completed').trigger('change.select2');
                                    }, 100);
                                }
                            });
                    } else {
                        handleError(response.message || '{{__("messages.something_went_wrong")}}', $select, previousValue);
                    }
                },
                error: function (xhr) {
                    handleError(xhr.responseJSON?.message || '{{__("messages.something_went_wrong")}}', $select, previousValue);
                }
            });
        });
    }

    function resetSelection($element, previousValue) {
        $element.val(previousValue).trigger('change.select2');
    }

    function refreshDataTable() {
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().ajax.reload(function() {
                // Restore completed status after reload
                $('.status-select').each(function() {
                    var $statusSelect = $(this);
                    var currentStatus = $statusSelect.data('current-status');
                    if (currentStatus === 'completed') {
                        $statusSelect.val('completed').trigger('change.select2');
                    }
                });
            }, false);
        } else if (typeof appointmentTable !== 'undefined') {
            appointmentTable.ajax.reload(function() {
                // Restore completed status after reload
                $('.status-select').each(function() {
                    var $statusSelect = $(this);
                    var currentStatus = $statusSelect.data('current-status');
                    if (currentStatus === 'completed') {
                        $statusSelect.val('completed').trigger('change.select2');
                    }
                });
            }, false);
        }
    }

    function handleError(message, $select, previousValue) {
        Swal.fire('Error!', message, 'error')
            .then(() => resetSelection($select, previousValue));
    }
});
</script>