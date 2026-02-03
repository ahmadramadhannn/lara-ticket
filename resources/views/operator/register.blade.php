<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Operator Registration') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Register your bus company') }}</p>
    </div>

    <form method="POST" action="{{ route('operator.register.store') }}">
        @csrf

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">{{ __('Account Information') }}</h3>
            
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div class="mt-4">
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">{{ __('PO Company Information') }}</h3>
            
            <!-- Operator Name -->
            <div>
                <x-input-label for="operator_name" :value="__('PO Name')" />
                <x-text-input id="operator_name" class="block mt-1 w-full" type="text" name="operator_name" :value="old('operator_name')" required />
                <x-input-error :messages="$errors->get('operator_name')" class="mt-2" />
            </div>

            <!-- Operator Code -->
            <div class="mt-4">
                <x-input-label for="operator_code" :value="__('PO Code')" />
                <x-text-input id="operator_code" class="block mt-1 w-full" type="text" name="operator_code" :value="old('operator_code')" required placeholder="{{ __('Unique code 2-10 characters') }}" />
                <x-input-error :messages="$errors->get('operator_code')" class="mt-2" />
            </div>

            <!-- Description -->
            <div class="mt-4">
                <x-input-label for="operator_description" :value="__('PO Description')" />
                <textarea id="operator_description" name="operator_description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Brief description about your company') }}">{{ old('operator_description') }}</textarea>
                <x-input-error :messages="$errors->get('operator_description')" class="mt-2" />
            </div>

            <!-- Contact Email -->
            <div class="mt-4">
                <x-input-label for="operator_email" :value="__('PO Contact Email')" />
                <x-text-input id="operator_email" class="block mt-1 w-full" type="email" name="operator_email" :value="old('operator_email')" required />
                <x-input-error :messages="$errors->get('operator_email')" class="mt-2" />
            </div>

            <!-- Contact Phone -->
            <div class="mt-4">
                <x-input-label for="operator_phone" :value="__('PO Contact Phone')" />
                <x-text-input id="operator_phone" class="block mt-1 w-full" type="text" name="operator_phone" :value="old('operator_phone')" required />
                <x-input-error :messages="$errors->get('operator_phone')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Submit Registration') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Already have account?') }} {{ __('Login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
