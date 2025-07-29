<div class="form-content mb-0">
    <div class="title mb-5 pb-md-3">
        <h3>{{__('messages.enter_basic_vendor_detail')}}</h3>
        <p>{{__('messages.explore_feature_create')}}</p>
    </div>
    <form id="step-form">
        @csrf
        <div class="row gy-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">{{ __('messages.profile_image') }}</label>
                    <div class="btn-file-upload">
                        <div class="mb-3 d-flex justify-content-center align-items-center text-center">
                            <img id="imagePreview" src="{{ asset('img/avatar/avatar.webp') }}" 
                                alt="Profile Image" class="img-thumbnail avatar-150 object-cover" >
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
                    </div>
                    @error('profile_image') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="col-md-8 col-lg-8">
                <div class="row gy-4">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">{{ __('messages.lbl_first_name') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="first_name" id="first_name" placeholder="{{ __('messages.placeholder_first_name') }}"
                            value="{{ old('first_name') }}" required>
                        @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.first_name_required') }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label">{{ __('messages.lbl_last_name') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="last_name" id="last_name"  placeholder="{{ __('messages.placeholder_last_name') }}"
                            value="{{ old('last_name') }}" required>
                        @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.last_name_required') }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="username" class="form-label">{{ __('messages.lbl_username') }}<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="{{ __('messages.placeholder_username') }}"
                            value="{{ old('username') }}" required>
                        @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.username_field_required') }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">{{ __('messages.lbl_email') }}<span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="{{ __('messages.placeholder_email') }}"
                            value="{{ old('email') }}" required>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="invalid-feedback">{{ __('messages.email_required') }}</div>
                    </div>                    

                    <div class="col-md-6">
                                    <label for="password" class="form-label">{{ __('messages.lbl_password') }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password"  placeholder="{{__('messages.placeholder_password')}}" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('password')">
                                            <i class="ph ph-eye"></i>
                                        </span>
                                        <div class="invalid-feedback">{{ __('messages.password_field_required') }}</div>
                                    </div>
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">{{ __('messages.lbl_confirm_password') }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirmation"  placeholder="{{__('messages.placeholder_confirm_password')}}"
                                        id="password_confirmation" required>
                                        <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('password_confirmation')">
                                            <i class="ph ph-eye"></i>
                                        </span>
                                        <div class="invalid-feedback">{{ __('messages.confirm_password_required') }}</div>
                                    </div>
                                    @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                    @if(GetSettingValue('vendor_commission_type')=='per_vendor')
                        <div class="col-md-6">
                            <label for="commission_type" class="form-label">{{ __('messages.commission_type') }}<span class="text-danger">*</span></label>
                            <select name="commission_type" id="commission_type" class="form-select select2" required>
                                <option value="" disabled selected>{{ __('messages.select_commission_type') }}</option>
                                <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>{{ __('messages.fixed') }}</option>
                                <option value="percentage" {{ old('commission_type') == 'percentage' ? 'selected' : '' }}>{{ __('messages.percentage') }}</option>
                            </select>
                            @error('commission_type') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="invalid-feedback">{{ __('messages.commission_type_required') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label for="commission" class="form-label">{{ __('messages.lbl_commission') }} <span id="commission_symbol"></span><span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="commission" id="commission" placeholder="{{ __('messages.placeholder_commission') }}"
                                value="{{ old('commission') }}" 
                                oninput="this.value = (this.value > 100 && $('#commission_type').val() === 'percentage') ? 100 : this.value"
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
            <input type="tel" class="form-control" value="{{ old('mobile') }}"
                name="mobile" id="mobile" placeholder="{{ __('messages.lbl_conatct_number') }}"
                required>
            @error('mobile')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <div class="invalid-feedback">{{ __('messages.contact_number_required') }}</div>
            </div>

            <div class="col-md-6 col-lg-4">
                <label class="form-label">Gender<span class="text-danger">*</span></label>
                <div class="d-flex align-items-center flex-sm-nowrap flex-wrap gap-3">
                    <label class="form-check form-control px-5 cursor-pointer">
                        <div>
                            <input class="form-check-input" type="radio" name="gender" id="male" value="male"
                                {{ old('gender', 'male') == 'male' ? 'checked' : '' }}>
                            <span class="form-check-label">{{ __('messages.lbl_male') }}</span>
                        </div>
                    </label>
                    <label class="form-check form-control px-5 cursor-pointer">
                        <div>
                            <input class="form-check-input" type="radio" name="gender" id="female" value="female"
                                {{ old('gender') == 'female' ? 'checked' : '' }}>
                            <span class="form-check-label">{{ __('messages.lbl_female') }}</span>
                        </div>
                    </label>
                    <label class="form-check form-control px-5 cursor-pointer">
                        <div>
                            <input class="form-check-input" type="radio" name="gender" id="other" value="other"
                                {{ old('gender') == 'other' ? 'checked' : '' }}>
                            <span class="form-check-label">{{ __('messages.other') }}</span>
                        </div>
                    </label>
                </div>
                @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6 col-lg-4">
                <label for="date_of_birth" class="form-label">{{ __('messages.lbl_date_of_birth') }} <span class="text-danger">*</span></label>
                <input type="date" class="form-control datetimepicker" 
                    value="{{ old('date_of_birth') }}" 
                    name="date_of_birth" 
                    id="date_of_birth" 
                    max="{{ date('Y-m-d') }}" 
                    placeholder="{{ __('messages.lbl_user_date_of_birth') }}" 
                    required>
                @error('date_of_birth')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="invalid-feedback">{{ __('messages.date_of_birth_required') }}</div>
            </div>        
        </div>
        <div class="mt-5 pt-5 d-flex justify-content-end">
            <button type="submit" class="btn btn-secondary next-btn">{{__('messages.next_step')}}</button>
        </div>
    </form>
</div>
<body>
@include('layouts.script')
   

    <script>
        
        $(document).ready(function() {
    // Image handling elements
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
        $imagePreview.attr('src', '{{ asset("default-image/Default-Image.jpg") }}');
        $(this).hide();
    });
            
          

            $('#step-form').on('submit', function(e) {
                let isValid = true;

                // Check all required fields
                $('input[required], select[required]').each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid').removeClass('is-valid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Validate password length
                const password = $('#password').val();
                if (password.length < 8) {
                    $('#password').addClass('is-invalid').removeClass('is-valid');
                    isValid = false;
                } else {
                    $('#password').removeClass('is-invalid').addClass('is-valid');
                }

                // Validate password match
                const confirmPassword = $('#password_confirmation').val();
                if (password !== confirmPassword) {
                    $('#password_confirmation').addClass('is-invalid').removeClass('is-valid');
                    $('#password_confirmation').next('.invalid-feedback').text("{{ __('messages.passwords_do_not_match') }}");
                    isValid = false;
                } else {
                    $('#password_confirmation').removeClass('is-invalid').addClass('is-valid');
                    $('#password_confirmation').next('.invalid-feedback').text('');
                }

                // If any validation fails, prevent form submission
                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Live validation for password fields
            $('#password, #password_confirmation').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                if (password.length < 8) {
                    $('#password').addClass('is-invalid').removeClass('is-valid');
                } else {
                    $('#password').removeClass('is-invalid').addClass('is-valid');
                }

                if (confirmPassword.length > 0) {
                    if (password !== confirmPassword) {
                        $('#password_confirmation').addClass('is-invalid').removeClass('is-valid');
                        $('#password_confirmation').next('.invalid-feedback').text("{{ __('messages.passwords_do_not_match') }}");
                    } else {
                        $('#password_confirmation').removeClass('is-invalid').addClass('is-valid');
                        $('#password_confirmation').next('.invalid-feedback').text('');
                    }
                }
            });

            // Mobile number validation (only numbers)
            $('#mobile').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Email validation
            $('#email').on('blur', function() {
                const email = $(this).val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (email && !emailRegex.test(email)) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                    $(this).next('.invalid-feedback').text("{{ __('messages.please_enter_a_valid_email_format') }}");
                } else if (email) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).next('.invalid-feedback').text('');
                }
            });
        });
    </script>
</body>
