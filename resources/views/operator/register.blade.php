<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Daftar Sebagai Operator PO') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Daftarkan perusahaan PO Anda untuk mulai menjual tiket bus.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('operator.register.store') }}">
        @csrf

        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-medium text-blue-900 mb-3">{{ __('Informasi Akun') }}</h3>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div class="mt-4">
                <x-input-label for="phone" :value="__('Nomor Telepon')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required placeholder="08xxxxxxxxxx" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <h3 class="font-medium text-green-900 mb-3">{{ __('Informasi Perusahaan PO') }}</h3>

            <!-- Operator Name -->
            <div>
                <x-input-label for="operator_name" :value="__('Nama PO')" />
                <x-text-input id="operator_name" class="block mt-1 w-full" type="text" name="operator_name" :value="old('operator_name')" required placeholder="PT. Bus Jaya" />
                <x-input-error :messages="$errors->get('operator_name')" class="mt-2" />
            </div>

            <!-- Operator Code -->
            <div class="mt-4">
                <x-input-label for="operator_code" :value="__('Kode PO')" />
                <x-text-input id="operator_code" class="block mt-1 w-full" type="text" name="operator_code" :value="old('operator_code')" required placeholder="BUSJAYA" maxlength="20" />
                <p class="text-xs text-gray-500 mt-1">{{ __('Kode unik untuk PO Anda (maks 20 karakter)') }}</p>
                <x-input-error :messages="$errors->get('operator_code')" class="mt-2" />
            </div>

            <!-- Operator Description -->
            <div class="mt-4">
                <x-input-label for="operator_description" :value="__('Deskripsi PO')" />
                <textarea id="operator_description" name="operator_description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Deskripsi singkat tentang PO Anda...">{{ old('operator_description') }}</textarea>
                <x-input-error :messages="$errors->get('operator_description')" class="mt-2" />
            </div>

            <!-- Operator Contact Email -->
            <div class="mt-4">
                <x-input-label for="operator_email" :value="__('Email Kontak PO')" />
                <x-text-input id="operator_email" class="block mt-1 w-full" type="email" name="operator_email" :value="old('operator_email')" required />
                <x-input-error :messages="$errors->get('operator_email')" class="mt-2" />
            </div>

            <!-- Operator Contact Phone -->
            <div class="mt-4">
                <x-input-label for="operator_phone" :value="__('Telepon Kontak PO')" />
                <x-text-input id="operator_phone" class="block mt-1 w-full" type="text" name="operator_phone" :value="old('operator_phone')" required placeholder="021-xxxxxxxx" />
                <x-input-error :messages="$errors->get('operator_phone')" class="mt-2" />
            </div>
        </div>

        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
            <p class="text-sm text-yellow-800">
                <strong>⚠️ Catatan:</strong> {{ __('Pendaftaran PO memerlukan persetujuan dari admin. Anda akan diberitahu setelah pendaftaran disetujui.') }}
            </p>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button>
                {{ __('Daftar Sebagai Operator') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
