<select class="form-select collector-select" data-appointment-id="{{ $data->id }}" style="width: 100%"
    @if ($data->status === 'completed' || $data->status === 'cancelled') disabled @endif>
    <option value="">{{ __('messages.select_collector') }}</option>

    @if (auth()->id() == $data->vendor_id || auth()->id() == $data->vendor_id)
        <option value="{{ auth()->id() }}" {{ $data->collector_id == auth()->id() ? 'selected' : '' }}>
            {{ __('messages.assign_to_myself') }} ({{ auth()->user()->first_name }} {{ auth()->user()->last_name }})
        </option>
    @endif

    @foreach ($collectors as $collector)
        <option value="{{ $collector->id }}" {{ $data->collector_id == $collector->id ? 'selected' : '' }}>
            {{ $collector->first_name }} {{ $collector->last_name }}
        </option>
    @endforeach
</select>

<script>
    $(document).ready(function() {

        $('.collector-select').select2();


        $('.collector-select').focus(function() {
            $(this).data('previous-value', $(this).val() || '');
        });

        $('.collector-select').change(function() {
            var select = $(this);
            var appointmentId = $(this).data('appointment-id');

            var collectorId = $(this).val();

            var collectorName = $(this).find('option:selected').text();
            var previousValue = $(this).data('previous-value');


            if (!collectorId) {
                select.val(previousValue).trigger('change.select2');
                return;
            }

            Swal.fire({
                title: "{{ __('messages.confirm_assignment') }}",
                text: `{{ __('messages.assign_collector_confirmation') }}`.replace(':name',
                    collectorName),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: "{{ __('messages.yes_assign') }}",
                cancelButtonText: "{{ __('messages.no_cancel') }}",
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: "{{ __('messages.assigning_collector') }}",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });


                    $.ajax({
                        url: '{{ route('backend.appointments.assign_collector') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: appointmentId,
                            collector_id: collectorId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: "{{ __('messages.success') }}",
                                    text: "{{ __('messages.collector_assigned_successfully') }}",
                                    icon: 'success'
                                }).then((result) => {

                                    select.data('previous-value',
                                        collectorId);


                                    if ($.fn.DataTable.isDataTable(
                                            '#datatable')) {
                                        $('#datatable').DataTable().ajax
                                            .reload(null, false);
                                    } else if (typeof appointmentTable !==
                                        'undefined') {
                                        appointmentTable.ajax.reload(null,
                                            false);
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: "{{ __('messages.error') }}",
                                    text: response.message ||
                                        "{{ __('messages.something_went_wrong') }}",
                                    icon: 'error'
                                });

                                select.val(previousValue).trigger('change.select2');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "{{ __('messages.error') }}",
                                text: xhr.responseJSON?.message ||
                                    "{{ __('messages.something_went_wrong') }}",
                                icon: 'error'
                            });

                            select.val(previousValue).trigger('change.select2');
                        }
                    });
                } else {

                    select.val(previousValue).trigger('change.select2');
                }
            });
        });
    });
</script>
