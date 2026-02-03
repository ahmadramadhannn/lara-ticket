<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Verifikasi Tiket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center mb-6">
                    <div class="text-6xl mb-4">🎫</div>
                    <h1 class="text-2xl font-bold mb-2">Verifikasi Tiket</h1>
                    <p class="text-gray-600">Masukkan kode booking untuk memverifikasi tiket</p>
                </div>

                <form action="{{ route('ticket-check.verify') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="booking_code" class="block text-sm font-medium text-gray-700 mb-1">
                            Kode Booking
                        </label>
                        <input type="text" id="booking_code" name="booking_code"
                            class="w-full text-center text-2xl font-mono tracking-widest uppercase rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="ABCD1234"
                            maxlength="20"
                            required
                            autofocus>
                        @error('booking_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Verifikasi
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t text-center text-sm text-gray-500">
                    <p>Anda juga dapat memindai QR code pada tiket penumpang</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
