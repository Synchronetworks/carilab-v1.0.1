@extends('backend.layouts.app')
@section('title', isset($data) ? __('messages.edit_lab_session') : __('messages.new_lab_session') )

@section('content')
    <div class="form-content">
        <form action="{{ route('backend.labsession.store') }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
            @csrf
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="card-input-title">
                    <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
                </div>
                <a href="{{ route('backend.labsession.index') }}" class="btn btn-sm btn-primary">
                    <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
                </a>
            </div>
            <div class="card">
                <div class="card-body">                        
                    <!-- Basic Information -->
                    <div class="row gy-4">
                        <div class="col-12">
                            <div class="form-group">
                                @if(isset($data))
                                    <!-- When editing, show lab name and use hidden input -->
                                    <label class="form-label">{{__('messages.lbl_lab')}} </label>
                                    <div class="mb-2">
                                        <input type="hidden" name="lab_id" value="{{ $data->lab_id }}">
                                        <input type="text" class="form-control" value="{{ $labs->find($data->lab_id)->name }}" readonly>
                                    </div>
                                @else
                                    <!-- When creating new, show lab selection dropdown -->
                                    <label class="form-label">{{__('messages.select_lab')}} </label>
                                    <div class="mb-2 d-flex justify-content-center align-items-center ">
                                        <select name="lab_id" class="form-select select2" required>
                                            <option value="" disabled selected>{{__('messages.select_lab')}} </option>
                                            @foreach($labs as $lab)
                                                <option value="{{$lab->id}}">{{$lab->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p class="text-danger">{{__('messages.all_lab_created')}}</p>
                                @endif
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>

            
            <!-- Business Hours -->
            <div class="card-input-title mb-3">
                <h4 class="m-0">{{__('messages.bussiness_hours')}} </h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-12">
                            @php
                                $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            @endphp
                            
                            @foreach($weekdays as $day)
                            <div class="list-group-item p-3 mb-3">
                                <div class="row align-items-center gy-1">
                                    <div class="col-sm-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check">
                                                <input class="form-check-input day-off" type="checkbox" 
                                                    name="days[{{$day}}][is_holiday]" value="1" 
                                                    id="{{$day}}-dayoff" 
                                                    {{ isset($data->days[$day]['is_holiday']) && $data->days[$day]['is_holiday'] ? 'checked' : '' }}>
                                            </div>
                                            <h6 class="text-capitalize m-0">{{ $day }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 time-inputs">
                                        <div class="d-flex align-items-center justify-content-sm-end gap-2">
                                            <input type="time" class="form-control min-datetimepicker-time" 
                                                name="days[{{$day}}][start_time]" 
                                                value="{{ $data->days[$day]['start_time'] ?? '09:00' }}">
                                            <input type="time" class="form-control min-datetimepicker-time" 
                                                name="days[{{$day}}][end_time]" 
                                                value="{{ $data->days[$day]['end_time'] ?? '18:00' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 text-end">
                                        <a href="#" class="text-primary add-break">{{__('messages.add_break')}} </a>
                                    </div>
                                    <div class="col-sm-6 text-end lab-close-container d-none">
                                        <h6 class="text-primary">{{__('messages.lab_close')}} </h6>
                                    </div>
                                </div>
                                
                                <div class="breaks-container">
                                    @if(isset($data->days[$day]['breaks']))
                                        @php
                                            $breaks = $data->days[$day]['breaks'];
                                        @endphp

                                        @foreach($breaks as $break)
                                            <div class="break-row row gy-4 align-items-center">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0 px-5">{{__('messages.break')}} </h6>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="time" class="form-control min-datetimepicker-time" name="days[{{$day}}][breaks][][start_break]"  value="{{ $break['start_break'] }}">
                                                        <input type="time" class="form-control min-datetimepicker-time"  name="days[{{$day}}][breaks][][end_break]"  value="{{ $break['end_break'] }}">                                                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger btn-sm remove-break">{{__('messages.remove')}} </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @endforeach
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


document.addEventListener('DOMContentLoaded', function() {
    // Handle day off toggles
    document.querySelectorAll('.day-off').forEach(checkbox => {
        // Trigger the change event for initially checked checkboxes
        if (checkbox.checked) {
            const timeInputs = checkbox.closest('.list-group-item').querySelector('.time-inputs');
            const breaksContainer = checkbox.closest('.list-group-item').querySelector('.breaks-container');
            const labCloseContainer = checkbox.closest('.list-group-item').querySelector('.lab-close-container');
            const addBreakButton =checkbox.closest('.list-group-item').querySelector('.add-break');
            timeInputs.classList.add('d-none');
            breaksContainer.classList.add('d-none');
            addBreakButton.classList.add('d-none');
            if (labCloseContainer) {
                labCloseContainer.classList.remove('d-none');
                
            }   
        }

        checkbox.addEventListener('change', function() {
            const timeInputs = this.closest('.list-group-item').querySelector('.time-inputs');
            const breaksContainer = this.closest('.list-group-item').querySelector('.breaks-container');
            const labCloseContainer = this.closest('.list-group-item').querySelector('.lab-close-container');
            const addBreakButton =checkbox.closest('.list-group-item').querySelector('.add-break');
            if (this.checked) {
                timeInputs.classList.add('d-none');
                breaksContainer.classList.add('d-none');
                labCloseContainer.classList.remove('d-none');
                addBreakButton.classList.add('d-none');
            } else {
                timeInputs.classList.remove('d-none');
                breaksContainer.classList.remove('d-none');
                labCloseContainer.classList.add('d-none');
                addBreakButton.classList.remove('d-none');
            }
        });
    });

    // Handle adding breaks
    document.querySelectorAll('.add-break').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const container = this.closest('.list-group-item').querySelector('.breaks-container');
            const day = this.closest('.list-group-item').querySelector('input[type="checkbox"]').id.replace('-dayoff', '');
            
            const breakHtml = `
                <div class="break-row row gy-3 align-items-center mt-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0 px-5">{{ __('messages.break') }}</h6>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <input type="time" class="form-control min-datetimepicker-time" name="days[${day}][breaks][][start_break]">
                            <input type="time" class="form-control min-datetimepicker-time" name="days[${day}][breaks][][end_break]">                            
                        </div>
                    </div>
                    <div class="col-sm-3 text-end">
                        <button type="button" class="btn btn-danger btn-sm remove-break">{{ __('messages.remove') }}</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', breakHtml);
        });
    });

    // Handle removing breaks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-break')) {
            e.target.closest('.break-row').remove();
        }
    });
});
</script>
@endpush
