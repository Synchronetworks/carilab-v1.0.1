<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 " />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('messages.lbl_email')" />

                <x-input id="email" class="" type="email" name="email" :value="old('email', $request->email)" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('messages.lbl_password')" />

                <x-input id="password" class="" type="password" name="password" required />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('messages.lbl_confirm_password')" />

                <x-input id="password_confirmation" class="" type="password" name="password_confirmation" required />
            </div>

            <div class="d-flex align-items-center justify-content-end mt-4">
                <x-button>
                    {{ __('messages.reset_password') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
