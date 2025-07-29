<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a>
                <x-application-logo />
            </a>
        </x-slot>

        <div class="my-4">
            {{ __('messages.forgot_password') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('messages.lbl_email')" />

                <x-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus />
                <div class="invalid-feedback" id="email-error"></div>
            </div>

            <div class="d-flex align-items-center justify-content-center mt-4">
                <x-button class="w-100">
                    {{ __('messages.reset_link') }}
                </x-button>
            </div>
        </form>
        <script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const form = document.getElementById('forgot-password-form');

    emailInput.addEventListener('blur', function() {
        validateEmail(this);
    });

    function validateEmail(input) {
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const value = input.value.trim();

        // Reset validation state
        input.classList.remove('is-invalid', 'is-valid');
        emailError.textContent = '';
        
        if (!value) {
            input.classList.add('is-invalid');
            emailError.textContent = __('messages.email_required');
            return false;
        }
        
        if (!emailRegex.test(value)) {
            input.classList.add('is-invalid');
            emailError.textContent = __('messages.enter_valid_email');
            return false;
        }
        
        input.classList.add('is-valid');
        return true;
    }

    form.addEventListener('submit', function(e) {
        if (!validateEmail(emailInput)) {
            e.preventDefault();
            emailInput.focus();
        }
    });
});
</script>
    </x-auth-card>
</x-guest-layout>
