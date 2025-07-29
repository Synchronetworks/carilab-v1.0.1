@extends('setting::backend.profile.profile-layout')

@section('profile-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="ph ph-user-circle"></i> {{__('messages.lbl_personal_info')}}</h2>
</div>


{{ html()->form('POST' ,route('backend.profile.information-update'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')
->attribute('id', 'form-submit')  // Add the id attribute here
->class('requires-validation')  // Add the requires-validation class
->attribute('novalidate', 'novalidate')  // Disable default browser validation
->open() }}
    @csrf
    <div class="row gy-4">
            <div class="col-md-8">
                <div class="row gy-4">
                    <div class="form-group col-md-6">
                        <label class="form-label" for="first_name">{{ __('messages.lbl_first_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.first_name_required')}}</div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="last_name">{{ __('messages.lbl_last_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.last_name_required')}}</div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="email">{{ __('messages.lbl_email') }} <span class="text-danger">*</span></label>
                        <input type="email" class="form-control " id="email" name="email" value="{{ old('email', $user->email) }}" required >
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="email-error">{{__('messages.email_required')}}</div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="mobile">{{ __('messages.lbl_contact_number') }} <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control " id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" required>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="name-error">{{__('messages.contact_number_required')}}</div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="" class="w-100">{{ __('messages.lbl_gender') }}</label>
                        <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap gap-3">
                            <label class="form-check form-control px-5 cursor-pointer">
                            <div >
                                <input class="form-check-input" type="radio" name="gender" id="male" value="male" {{ old('gender', $user->gender) == 'male' ? 'checked' : '' }} />
                                <span class="form-check-label">  {{__('messages.lbl_male')}} </span>
                            </div>
                        </label>
                        <label class="form-check form-control px-5 cursor-pointer">
                            <div>
                                <input class="form-check-input" type="radio" name="gender" id="female" value="female" {{ old('gender', $user->gender) == 'female' ? 'checked' : '' }} />
                                <span class="form-check-label" for="female">  {{__('messages.lbl_female')}} </span>
                            </div>
                        </label>
                        <label class="form-check form-control px-5 cursor-pointer">
                            <div class="">
                                <input class="form-check-input" type="radio" name="gender" id="other" value="other" {{ old('gender', $user->gender) == 'other' ? 'checked' : '' }} />
                                <span class="form-check-label" for="other"> {{__('messages.lbl_other')}} </span>
                            </div>
                        </label>
                        </div>

                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

            @if(auth()->user()->user_type=='vendor')
                    <div class="col-md-6 ">
                        <label for="date_of_birth" class="form-label">{{ __('messages.lbl_date_of_birth') }}
                            <span class="text-danger">*</span></label>
                        <input type="date" class="form-control datetimepicker" value="{{ old('date_of_birth', $user->date_of_birth) }}" name="date_of_birth" id="date_of_birth" max="{{ date('Y-m-d') }}" placeholder="{{ __('messages.lbl_user_date_of_birth') }}" required>
                        @error('date_of_birth')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="invalid-feedback" id="date_of_birth-error">{{ __('messages.date_birth_required') }}
                        </div>
                    </div>
                    @endif
                </div>

            @if(auth()->user()->user_type=='vendor')
                <div class="col-md-12">
                    <label for="address" class="form-label">{{ __('messages.lbl_address') }}</label>
                    <textarea class="form-control" name="address" id="address" rows="3">{{ old('address', $user->address) }}</textarea>
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                @endif
               
            </div>
           
            <div class="col-md-4 position-relative">
                <div class="btn-file-upload">
                    <div class="mb-2 d-flex justify-content-center align-items-center text-center">
                        <img id="imagePreview" src="{{ $user->getProfileImageAttribute() ?? asset('images/default-avatar.png') }}" alt="Profile Image" class="img-thumbnail avatar-150">
                    </div>
                    <div class="d-flex justify-content-center align-items-center text-center flex-wrap row-gap-2 column-gap-3">
                        <button type="button" class="btn btn-sm btn-primary" id="uploadButton">
                            {{ __('messages.upload_image') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" id="removeButton" style="display: none;">
                            {{ __('messages.remove_image') }}
                        </button>
                    </div>
                </div>

                <input type="file" name="profile_image" id="profileImageInput" class="form-control d-none" accept="image/*">
                        @error('profile_image') <small class="text-danger">{{ $message }}</small> @enderror
                    
            </div>   

            @if(auth()->user()->user_type=='vendor')
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <label for="country" class="form-label">{{ __('messages.country') }}<span class="text-danger">*</span></label>
                    <select name="country_id" class="form-select select2" id="country" required>
                        <option value="" disabled>{{ __('messages.select_country') }}</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.country_required') }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="state" class="form-label">{{ __('messages.state') }}<span class="text-danger">*</span></label>
                    <select name="state_id" class="form-select select2" id="state" required>
                        <option value="" disabled>{{ __('messages.select_state') }}</option>
                        @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id', $user->state_id) == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('state_id') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.state_required') }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="city" class="form-label">{{ __('messages.city') }}<span class="text-danger">*</span></label>
                    <select name="city_id" class="form-select select2" id="city" required>
                        <option value="" disabled>{{ __('messages.select_city') }}</option>
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="invalid-feedback">{{ __('messages.city_required') }}</div>
                </div>
            </div>
            @endif
            <div class="form-group col-md-12 text-end">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-right')->id('submit-button') }}
            </div>
            
    </div>
</form>
@endsection
@push('after-scripts')




<script>
$(document).ready(function() {
    const $uploadButton = $('#uploadButton');
    const $removeButton = $('#removeButton');
    const $profileImageInput = $('#profileImageInput');
    const $imagePreview = $('#imagePreview');

    $uploadButton.on('click', function() {
        $profileImageInput.trigger('click');
    });

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

    $removeButton.on('click', function() {
        $profileImageInput.val('');
        $imagePreview.attr('src', '{{ asset("images/default-avatar.png") }}');
        $(this).hide();
    });
});

</script>
@endpush
