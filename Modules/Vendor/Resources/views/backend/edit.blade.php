@extends('backend.layouts.app')
@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="form-content">
        <form action="{{ route('backend.vendors.update', $data->id) }}" method="POST" enctype="multipart/form-data" class='requires-validation' id="form-submit" novalidate>
            @csrf
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="card-input-title">
                    <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
                </div>
                <a href="{{ route('backend.vendors.index') }}" class="btn btn-sm btn-primary">
                    <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
                </a>
            </div>  
            <div class="card">
                <div class="card-body">
                    @method('PUT')
                    {{ html()->hidden('id',$data->id ?? null) }}
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{__('messages.profile_image')}}</label>
                                    <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                                        <img id="imagePreview" 
                                        src="{{ $data->profile_image ?? asset('images/default-avatar.png') }}" 
                                        alt="Profile Image" class="img-thumbnail avatar-150 object-cover">
                                </div>
                                    <div class="d-flex justify-content-center align-items-center text-center gap-3">
                                        <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                                            {{ __('messages.upload_image') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" id="removeButton" style="display: none;">
                                            {{ __('messages.remove_image') }}
                                        </button>
                                </div>
                                <input type="file" name="profile_image" id="profileImageInput" class="form-control d-none" accept="image/*">
                                @error('profile_image') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>                            
                        </div>
                        <div class="col-md-6 col-lg-8">
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">{{ __('messages.lbl_first_name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" 
                                        value="{{ old('first_name', $data->first_name) }}" required>
                                    @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.first_name_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">{{ __('messages.lbl_last_name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" 
                                        value="{{ old('last_name', $data->last_name) }}" required>
                                    @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.last_name_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ __('messages.lbl_email') }}<span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email" 
                                        value="{{ old('email', $data->email) }}" required>
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.email_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="username" class="form-label">{{ __('messages.lbl_username') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username" id="username" 
                                    value="{{ old('username', $data->username) }}" required>
                                @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.username_field_required') }}</div>
                                </div>                              
                            
                                    @if(Setting('vendor_commission_type')=='per_vendor')
                                <div class="col-md-6">
                                    <label for="commission_type" class="form-label">{{ __('messages.commission_type') }}<span class="text-danger">*</span></label>
                                    <select name="commission_type" id="commission_type" class="form-select select2" required>
                                        <option value="" disabled>{{ __('messages.select_commission_type') }}</option>
                                        <option value="fixed" {{ old('commission_type', $data->commission_type) == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed') }}</option>
                                        <option value="percentage" {{ old('commission_type', $data->commission_type) == 'percentage' ? 'selected' : '' }}>{{ __('messages.percentage') }}</option>
                                    </select>
                                    @error('commission_type') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.commission_type_required') }}</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="commission" class="form-label">{{ __('messages.lbl_commission') }} <span id="commission_symbol"></span><span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="commission" id="commission" 
                                        value="{{ old('commission', $data->commission) }}"            oninput="this.value = (this.value > 100 && $('#commission_type').val() === 'percentage') ? 100 : this.value"
                                        required>
                                    @error('commission') <span class="text-danger">{{ $message }}</span> @enderror
                                    <div class="invalid-feedback">{{ __('messages.commission_field_required') }}</div>
                                </div>
                                @endif                                
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="mobile" class="form-label">{{ __('messages.lbl_contact_number') }}<span
                                class="text-danger">*</span></label>
                                <input type="tel" class="form-control"
                                value="{{ old('mobile',$data->mobile) }}" name="mobile" id="mobile"
                                placeholder="{{ __('messages.placeholder_contact_number') }}" required>
                            <div class="invalid-feedback" id="mobile-error">{{ __('messages.phone_required') }}</div>
                        @error('mobile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="date_of_birth" class="form-label">{{ __('messages.lbl_date_of_birth') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control datetimepicker" 
                            value="{{ old('date_of_birth', $data->date_of_birth) }}" 
                            name="date_of_birth" 
                                id="date_of_birth" 
                                max="{{ date('Y-m-d') }}" 
                                placeholder="{{ __('messages.placeholder_date_of_birth') }}" 
                                required>
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback" id="date_of_birth-error">{{ __('messages.date_birth_required') }}</div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">{{ __('messages.lbl_gender') }}<span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center flex-sm-nowrap flex-wrap gap-3">
                                <label class="form-check form-control px-5 cursor-pointer">
                                    <div>
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male"
                                        {{ old('gender', $data->gender) == 'male' ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ __('messages.lbl_male') }}</span>
                                    </div>
                                </label>
                                <label class="form-check form-control px-5 cursor-pointer">
                                    <div>
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female"
                                        {{ old('gender', $data->gender) == 'female' ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ __('messages.lbl_female') }}</span>
                                    </div>
                                </label>
                                <label class="form-check form-control px-5 cursor-pointer">
                                    <div>
                                        <input class="form-check-input" type="radio" name="gender" id="other" value="other"
                                        {{ old('gender', $data->gender) == 'other' ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ __('messages.lbl_other') }}</span>
                                    </div>
                                </label>
                            </div>
                            @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>                                            

                        <div class="col-md-6 col-lg-4">
                            <label for="country" class="form-label">{{ __('messages.country') }}<span class="text-danger">*</span></label>
                            <select name="country_id" class="form-select select2" id="country" required>
                                <option value="" disabled>{{ __('messages.select_country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country', $data->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.country_required') }}</div>
                        </div>

                          <div class="col-md-6 col-lg-4">
                            <label for="state" class="form-label">{{ __('messages.state') }}<span class="text-danger">*</span></label>
                            <select name="state_id" class="form-select select2" id="state" required>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state', $data->state_id) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.state_required') }}</div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="city" class="form-label">{{ __('messages.city') }}<span class="text-danger">*</span></label>
                            <select name="city_id" class="form-select select2" id="city" required>
                                <option value="" disabled>{{ __('messages.select_city') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city', $data->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.city_required') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tax_id" class="form-label">{{ __('messages.lbl_tax') }}<span class="text-danger">*</span></label>
                            <select name="tax_id[]" id="tax_id" class="form-select select2" multiple>
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}" 
                                        {{ in_array($tax->id, old('tax_id', $data->tax ? $data->tax : [])) ? 'selected' : '' }}>
                                        {{ $tax->title }} ({{ $tax->value }}%)
                                    </option>
                                @endforeach
                            </select>

                            @error('tax_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>             

                        <div class="col-md-6 col-lg-4">
                            <label for="status" class="form-label">{{ __('messages.lbl_status') }}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="status" class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" 
                                    value="1" {{ old('status', $data->status) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="set_as_featured" class="form-label">{{ __('messages.set_featured') }}</label>
                            <div class="d-flex align-items-center justify-content-between form-control">
                                <label for="set_as_featured" class="form-label mb-0 text-body">{{ __('messages.featured') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="set_as_featured" value="0">
                                    <input class="form-check-input" type="checkbox" id="set_as_featured" name="set_as_featured" 
                                    value="1" {{ old('set_as_featured', $data->set_as_featured) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            @error('set_as_featured') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <label for="address" class="form-label">{{ __('messages.lbl_address') }}<span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" id="address" rows="6" placeholder="{{ __('messages.placeholder_address') }}" required>{{ old('address', $data->address) }}</textarea>
                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.address_required') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="submit" id='submit-button' class="btn btn-primary">
                    {{__('messages.save')}}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('after-scripts')
<script>
    document.getElementById('mobile').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    function validateNameInput(inputId, errorId) {
        document.getElementById(inputId).addEventListener('input', function (e) {
            let regex = /^[A-Za-z\s]*$/;  // Only letters and spaces allowed
            let inputValue = this.value;
            if (!regex.test(inputValue)) {
                this.value = inputValue.replace(/[^A-Za-z\s]/g, ''); // Remove invalid characters
                document.getElementById(errorId).classList.remove('d-none'); // Show error message
            } else {
                document.getElementById(errorId).classList.add('d-none'); // Hide error message
            }
        });
    }

    // Apply validation to First Name and Last Name
    $(document).ready(function() {
    const $uploadButton = $('#uploadButton');
    const $removeButton = $('#removeButton');
    const $profileImageInput = $('#profileImageInput');
    const $imagePreview = $('#imagePreview');

    // Upload Button Click
    $uploadButton.on('click', function() {
        $profileImageInput.trigger('click');
    });

    // Image Preview
    $profileImageInput.on('change', function() {
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
        $profileImageInput.val('');
        $imagePreview.attr('src', '{{ asset("images/default-avatar.png") }}');
        $(this).hide();
    });
});
 document.addEventListener('DOMContentLoaded', function() {

    flatpickr("#date_of_birth", {
            dateFormat: "Y-m-d", // Format as YYYY-MM-DD
            maxDate: "today", // Disable future dates
        });
    const countrySelect = document.getElementById('country');
    $(countrySelect).select2();

    // Pre-populate the state and city if a country is selected on page load
    const selectedCountryId = "{{ old('country', $data->country_id) }}";
    if (selectedCountryId) {
        fetchStates(selectedCountryId); // Fetch states based on the selected country
    }

    // Use select2:select event for capturing the change
    $(countrySelect).on('select2:select', function (e) {
        const countryId = e.params.data.id;
        clearStateAndCity();
        fetchStates(countryId);
    });

    const stateSelect = document.getElementById('state');
    $(stateSelect).select2();
    // Pre-populate the state and city if a country is selected on page load
    const selectedStateId = "{{ old('state', $data->state_id) }}";
    if (selectedStateId) {
        fetchCities(selectedStateId); // Fetch states based on the selected country
    }

    // Use select2:select event for capturing the change
    $(stateSelect).on('select2:select', function (e) {
        const countryId = e.params.data.id;
        clearCity();
        fetchCities(countryId);
    });

    
});

function clearStateAndCity() {
        const stateSelect = $('#state');
        const citySelect = $('#city');
        stateSelect.empty().append('<option value="" disabled selected>Select State</option>').select2();
        citySelect.empty().append('<option value="" disabled selected>Select City</option>').select2();
    }

    // Function to clear city field
    function clearCity() {
        const citySelect = $('#city');
        citySelect.empty().append('<option value="" disabled>Select City</option>').select2();
    }

// Function to fetch states based on the selected country
function fetchStates(countryId) {
    const url = `{{ route('backend.state.index_list') }}?country_id=${countryId}`;
        $.ajax({
            url: url,
            type: 'GET',
            success: function (states) {
                const stateSelect = $('#state');
                stateSelect.empty(); // Clear the previous options
                stateSelect.append('<option value="" disabled selected>Select State</option>');
                
                // Add new state options
                states.forEach(state => {
                    const isSelected = state.id == "{{ old('state', $data->state_id) }}" ? 'selected' : '';
                    stateSelect.append(`<option value="${state.id}" ${isSelected}>${state.name}</option>`);
                });

                // Trigger state select change to fetch cities if state exists
                const selectedStateId = "{{ old('state', $data->state_id) }}";
                if (selectedStateId) {
                    fetchCities(selectedStateId);
                }
                $(stateSelect).select2(); // Re-initialize Select2
            }
        });
}



    function fetchCities(stateId) {
        // Fetch cities using AJAX
        const url = `{{ route('backend.city.index_list') }}?state_id=${stateId}`;
        $.ajax({
            url: url,
            type: 'GET',
            success: function (cities) {
                const citySelect = $('#city');
                citySelect.empty(); // Clear the previous options
                citySelect.append('<option value="" disabled selected>Select City</option>');
                
                // Add new city options
                cities.forEach(city => {
                    const isSelected = city.id == "{{ old('city', $data->city_id) }}" ? 'selected' : '';
                    citySelect.append(`<option value="${city.id}" ${isSelected}>${city.name}</option>`);
                });
                $(citySelect).select2(); // Re-initialize Select2
            }
        });
    }

    $(document).ready(function () {
        function updateCommissionSymbol() {
            let commissionType = $('#commission_type').val();
            let symbolSpan = $('#commission_symbol');
    
            if (commissionType === "percentage") {
                symbolSpan.text('(%)');
            } else if (commissionType === "fixed") {
                symbolSpan.text("{{ getCurrencySymbol() }}"); // Ensure this function works in Blade
            } else {
                symbolSpan.text('');
            }
        }

        // Call function initially (for preselected value)
        updateCommissionSymbol();

        // Listen for changes in commission_type select box
        $('#commission_type').on('change', updateCommissionSymbol);
    });
// Add this inside your existing DOMContentLoaded event listener
document.getElementById('email').addEventListener('blur', function() {
    validateEmail(this);
});

function validateEmail(emailInput) {
    const errorDiv = emailInput.nextElementSibling;
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    // Reset validation state
    emailInput.classList.remove('is-invalid', 'is-valid');
    
    if (!emailInput.value) {
        emailInput.classList.add('is-invalid');
        errorDiv.textContent = 'Email field is required';
        return false;
    }
    
    if (!emailRegex.test(emailInput.value)) {
        emailInput.classList.add('is-invalid');
        errorDiv.textContent = 'Please enter a valid email format (e.g., name@example.com)';
        return false;
    }
    
    emailInput.classList.add('is-valid');
    errorDiv.textContent = '';
    return true;
}

// Update your form submit handler to include email validation
const form = document.getElementById('form-submit');
form.addEventListener('submit', function(e) {
    const emailInput = document.getElementById('email');
    if (!validateEmail(emailInput)) {
        e.preventDefault();
        emailInput.focus();
    }
});
   


</script>
@endpush
