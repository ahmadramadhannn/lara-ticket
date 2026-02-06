<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Operator Registration') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Register your bus company') }}</p>
    </div>

    {{-- Registration Guidelines --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-semibold text-blue-900 mb-2">{{ __('Registration Requirements:') }}</h3>
        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
            <li>{{ __('Business license document (required)') }}</li>
            <li>{{ __('Detailed company description (min. 100 characters)') }}</li>
            <li>{{ __('Valid Indonesian phone number') }}</li>
            <li>{{ __('Approval typically takes 24-48 hours') }}</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('operator.register.store') }}" enctype="multipart/form-data">
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
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required placeholder="08xxxxxxxxxx or +62xxxxxxxxxx" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                <p class="text-xs text-gray-500 mt-1">{{ __('Format: +62 or 08 followed by 9-12 digits') }}</p>
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
                <x-text-input id="operator_code" class="block mt-1 w-full" type="text" name="operator_code" :value="old('operator_code')" required placeholder="{{ __('e.g. ABC, XYZ123') }}" />
                <x-input-error :messages="$errors->get('operator_code')" class="mt-2" />
                <p class="text-xs text-gray-500 mt-1">{{ __('Unique code, alphanumeric only, max 20 characters') }}</p>
            </div>

            <!-- Description -->
            <div class="mt-4">
                <x-input-label for="operator_description" :value="__('PO Description')" />
                <textarea id="operator_description" name="operator_description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Detailed description about your company (minimum 100 characters)') }}" required>{{ old('operator_description') }}</textarea>
                <div class="flex justify-between items-center mt-1">
                    <x-input-error :messages="$errors->get('operator_description')" />
                    <span class="text-xs text-gray-500" id="char-count">0/100</span>
                </div>
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
                <x-text-input id="operator_phone" class="block mt-1 w-full" type="text" name="operator_phone" :value="old('operator_phone')" required placeholder="08xxxxxxxxxx or +62xxxxxxxxxx" />
                <x-input-error :messages="$errors->get('operator_phone')" class="mt-2" />
            </div>
        </div>

        {{-- Document Uploads --}}
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">{{ __('Required Documents') }}</h3>
            
            <!-- Business License -->
            <div>
                <x-input-label for="business_license" :value="__('Business License') . ' *'" />
                <input id="business_license" type="file" name="business_license" accept=".pdf,.jpg,.jpeg,.png" required class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <x-input-error :messages="$errors->get('business_license')" class="mt-2" />
                <p class="text-xs text-gray-500 mt-1">{{ __('PDF, JPG, or PNG. Max 5MB') }}</p>
            </div>

            <!-- Business Permit (Optional) -->
            <div class="mt-4">
                <x-input-label for="business_permit" :value="__('Business Permit (Optional)')" />
                <input id="business_permit" type="file" name="business_permit" accept=".pdf,.jpg,.jpeg,.png" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <x-input-error :messages="$errors->get('business_permit')" class="mt-2" />
            </div>

            <!-- Tax ID Document (Optional) -->
            <div class="mt-4">
                <x-input-label for="tax_id_document" :value="__('Tax ID Document (Optional)')" />
                <input id="tax_id_document" type="file" name="tax_id_document" accept=".pdf,.jpg,.jpeg,.png" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <x-input-error :messages="$errors->get('tax_id_document')" class="mt-2" />
            </div>
        </div>

        {{-- Hidden reCAPTCHA token field --}}
        <input type="hidden" name="recaptcha_token" id="recaptcha-token">
        <x-input-error :messages="$errors->get('recaptcha_token')" class="mt-2" />

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

    {{-- reCAPTCHA v3 Script --}}
    @if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script>
        // Character counter for description
        const descriptionField = document.getElementById('operator_description');
        const charCount = document.getElementById('char-count');
        
        descriptionField.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length + '/100';
            if (length >= 100) {
                charCount.classList.remove('text-red-500');
                charCount.classList.add('text-green-600');
            } else {
                charCount.classList.remove('text-green-600');
                charCount.classList.add('text-red-500');
            }
        });

        // reCAPTCHA v3
        grecaptcha.ready(function() {
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'operator_registration'})
                    .then(function(token) {
                        document.getElementById('recaptcha-token').value = token;
                        e.target.submit();
                    });
            });
        });
    </script>
    @endif
</x-guest-layout>
