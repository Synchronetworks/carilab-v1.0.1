@extends('setting::backend.profile.profile-layout')

@section('profile-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="ph ph-key"></i> {{ __('messages.change_password') }}</h2>
</div>

<form method="POST" action="{{ route('backend.profile.change_password') }}" class="requires-validation" novalidate id="form-submit">
    @csrf

    <div class="form-group mb-4">
        <label class="form-label" for="old_password">{{ __('messages.lbl_old_password') }}<span class="text-danger">*</span></label>
        <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password" name="old_password" placeholder="{{__('messages.enter_old_password')}}" required>
        @error('old_password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div class="invalid-feedback" id="old-pass-error">{{__('messages.old_password_required')}}</div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="new_password">{{ __('messages.lbl_new_password') }}<span class="text-danger">*</span></label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               placeholder="{{__('messages.enter_new_password')}}"
               minlength="8"  
               required>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div class="invalid-feedback" id="new-pass-error">{{__('messages.password_max_length')}}</div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="confirm_password">{{ __('messages.lbl_confirm_password') }}<span class="text-danger">*</span></label>
        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="{{__('messages.enter_confirm_password')}}" required>
        @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div class="invalid-feedback" id="confirm-pass-error">{{__('messages.confirm_password_required')}}</div>
        <div class="invalid-feedback d-none " id="confirm-pass-match-error" >{{__('messages.confirm_password_mismatch')}}</div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary" id="submit-button" disabled>
            {{ __('messages.submit') }}
        </button>
    </div>
</form>

<script>
    const newPasswordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');
    const submitButton = document.getElementById('submit-button');
    const confirmError = document.getElementById('confirm-pass-error');
    const confirmMatchError = document.getElementById('confirm-pass-match-error');
    const newPassError = document.getElementById('new-pass-error');

    function validatePasswords() {
        const newPassword = newPasswordField.value;
        const confirmPassword = confirmPasswordField.value;

        // Check password length
        if (newPassword.length < 8) {
            newPassError.style.display = 'block';
            submitButton.disabled = true;
            return false;
        } else {
            newPassError.style.display = 'none';
        }

        // Check if passwords match
        if (newPassword === confirmPassword && newPassword.length >= 8) {
            confirmMatchError.style.display = 'none';
            confirmMatchError.classList.add('d-none');
            submitButton.disabled = false;
        } else {
            if (confirmPassword === '') {
                confirmError.style.display = 'block';
                confirmMatchError.style.display = 'none';
                confirmMatchError.classList.add('d-none');
                submitButton.disabled = true;
            } else {
                confirmError.style.display = 'none';
                if (newPassword !== confirmPassword) {
                    confirmMatchError.style.display = 'block';
                    confirmMatchError.classList.remove('d-none');
                    submitButton.disabled = true;
                }
            }
        }
    }

    // Add oninput event listeners to both fields
    newPasswordField.addEventListener('input', validatePasswords);
    confirmPasswordField.addEventListener('input', validatePasswords);
</script>

@endsection



