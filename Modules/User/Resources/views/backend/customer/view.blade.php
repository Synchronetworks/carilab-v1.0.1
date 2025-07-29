@extends('backend.layouts.app')

@section('content')
    <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-3">
        <a href="{{ route('backend.users.index') }}" class="btn btn-sm btn-primary">
            <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
        </a>
    </div>
   
            <!-- Tabs -->
            <div class="d-flex align-items-center gap-2 mb-4">

                <div class="custom-tab-slider">
                    <ul class="nav nav-pills" id="customerTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview">{{__('messages.overview')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="appointments-tab" data-bs-toggle="tab" href="#appointments">{{__('messages.appointments')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payments-tab" data-bs-toggle="tab" href="#payments">{{__('messages.payment_history')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ratings-tab" data-bs-toggle="tab" href="#ratings">{{__('messages.ratings_reviews')}}</a>
                        </li>
                    </ul>
                </div>
               
            </div>

            <!-- Tab Content -->
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="customerTabContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview">
                            <!-- Appointment Statistics Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{__('messages.lbl_total_appointments')}}</h6>
                                            <h3 class="card-text">{{ $totalAppointments ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{__('messages.cancelled_appointments')}}</h6>
                                            <h3 class="card-text">{{ $cancelledAppointments ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{__('messages.completed_appointments')}}</h6>
                                            <h3 class="card-text">{{ $completedAppointments ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{__('messages.upcoming_appointments')}}</h6>
                                            <h3 class="card-text">{{ $upcomingAppointments ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Info Card -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">{{__('messages.basic_information')}}</h5>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="accountStatus" 
                                            {{ $data->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="accountStatus">
                                            {{ $data->status ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-3">
                                        <!-- Profile Image Column -->
                                            <div class="avatar-wrapper">
                                                <img src="{{ setBaseUrlWithFileName($data->file_url) }}" 
                                                    alt="{{ $data->first_name }}"
                                                    class="img-fluid rounded-circle avatar-150 object-cover">
                                            </div>
                                        
                                        <!-- Info Column -->
                                        <div class="flex-grow-1">
                                        <div class="d-flex align-items-center column-gap-3 row-gap-2 mt-2 flex-md-nowrap flex-wrap">
                                            <h4 class="mb-0">{{ $data->first_name ?? '--' }} {{ $data->last_name ?? '--' }}</h4>
                                            <div class="d-flex align-items-center mb-3 gap-1 text-break">
                                                <i class="ph ph-envelope-simple me-2"></i>
                                                <a href="mailto:{{ $data->email }}" class="text-decoration-none text-body">
                                                    {{ $data->email ?? '--' }}
                                                </a>
                                            </div>
                                            
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="ph ph-phone me-2"></i>
                                                <a href="tel:{{ $data->mobile }}" class="text-decoration-none text-body">
                                                    {{ $data->mobile ?? '--' }}
                                                </a>
                                            </div>
                                            
                                            <div class="d-flex align-items-center">
                                                <i class="ph ph-map-pin me-2"></i>
                                                <span>{{ $data->address ?? '--' }}</span>
                                            </div>
                                        </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointments Tab -->
                        <div class="tab-pane fade" id="appointments">
                            <!-- Add appointments content here -->
                            <h5>{{__('messages.appointment_history')}}</h5>
                            <!-- Add your appointments table/content -->
                        </div>

                        <!-- Payment History Tab -->
                        <div class="tab-pane fade" id="payments">
                            <h5>{{__('messages.payment_history')}}</h5>
                            <!-- You can move your existing subscription table here -->
                        </div>

                        <!-- Ratings & Reviews Tab -->
                        <div class="tab-pane fade" id="ratings">
                            <h5>{{__('messages.ratings_reviews')}}</h5>
                            <!-- Add ratings and reviews content -->
                        </div>
                    </div>
                </div>
            </div>
@endsection
