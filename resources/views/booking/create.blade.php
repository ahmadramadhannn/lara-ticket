<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Pemesanan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Summary -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="font-bold text-lg mb-4">Ringkasan Pemesanan</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Rute</p>
                            <p class="font-medium">{{ $schedule->route->originTerminal->name }} → {{ $schedule->route->destinationTerminal->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal & Waktu</p>
                            <p class="font-medium">{{ $schedule->departure_time->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">PO Bus</p>
                            <p class="font-medium">{{ $schedule->busOperator->name }} - {{ $schedule->bus->busClass->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Kursi</p>
                            <p class="font-medium text-indigo-600">{{ $seat }}</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <form action="{{ route('booking.store', $schedule) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="seat" value="{{ $seat }}">

                    <div>
                        <label for="passenger_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Penumpang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="passenger_name" name="passenger_name"
                            value="{{ old('passenger_name', auth()->user()->name) }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Nama sesuai KTP">
                        @error('passenger_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="passenger_id_number" class="block text-sm font-medium text-gray-700 mb-1">
                            No. KTP (opsional)
                        </label>
                        <input type="text" id="passenger_id_number" name="passenger_id_number"
                            value="{{ old('passenger_id_number') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Nomor KTP 16 digit">
                        @error('passenger_id_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price Summary -->
                    <div class="border-t pt-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Harga Tiket</span>
                            <span class="text-2xl font-bold text-indigo-600">{{ $schedule->formatted_price }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('schedules.show', $schedule) }}" class="text-gray-600 hover:text-gray-800">
                            ← Kembali
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                            Pesan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
