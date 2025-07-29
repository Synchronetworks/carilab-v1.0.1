<x-auth-layout>
    <x-slot name="title">
        @lang('Register')
    </x-slot>

    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 " />
            </a>
        </x-slot>

       
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        


        <form method="POST" action="{{ route('register') }}">
            @csrf

            
            <div class="mt-4">
                <x-label for="first_name" :value="__('messages.lbl_first_name')" />

                <x-input id="first_name" type="text" name="first_name" :value="old('first_name')" required autofocus />
            </div>

            
            <div class="mt-4">
                <x-label for="last_name" :value="__('messages.lbl_last_name')" />

                <x-input id="last_name" type="text" name="last_name" :value="old('last_name')" required autofocus />
            </div>
            
            <div class="mt-4">
                <x-label for="mobile" :value="__('messages.lbl_contact_number')" />

                <x-input id="mobile" type="number" name="mobile" required />
            </div>
           
            <div class="mt-4">
                <x-label for="email" :value="__('messages.email')" />

                <x-input id="email" type="email" name="email" :value="old('email')" required />
            </div>

            
            <div class="mt-4">
                <x-label for="password" :value="__('messages.lbl_password')" />

                <x-input id="password" type="password" name="password" required autocomplete="new-password" />
            </div>

            
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('messages.lbl_confirm_password')" />

                <x-input id="password_confirmation" type="password" name="password_confirmation" required />
            </div>



            <div class="flex items-center justify-end mt-4">

                <x-button class="ml-4 w-100">
                    {{ __('messages.register') }}
                </x-button>
            </div>
        </form>

        <x-slot name="extra">
            <span>
                {{ __('messages.already_register') }} <a href="{{ route('login') }}">{{__('messages.login')}}</a>.
            </span>
        </x-slot>
    </x-auth-card>
</x-auth-layout>
