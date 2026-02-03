<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Pencarian
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $origin->name }} → {{ $destination->name }}
                        </p>
                        <p class="text-gray-600">
                            {{ $date->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                    <a href="{{ route('home') }}" class="mt-4 md:mt-0 text-indigo-600 hover:text-indigo-800">
                        ← Ubah Pencarian
                    </a>
                </div>
            </div>

            <!-- Results -->
            @if($schedules->isEmpty())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-yellow-700">
                        Tidak ada jadwal tersedia untuk rute dan tanggal yang dipilih.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($schedules as $schedule)
                        <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                    <!-- Operator & Class -->
                                    <div class="mb-4 lg:mb-0">
                                        <p class="font-bold text-lg text-gray-800">{{ $schedule->busOperator->name }}</p>
                                        <span class="inline-block px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">
                                            {{ $schedule->bus->busClass->name }}
                                        </span>
                                    </div>

                                    <!-- Time & Route -->
                                    <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-gray-800">{{ $schedule->departure_time->format('H:i') }}</p>
                                            <p class="text-sm text-gray-500">{{ $schedule->route->originTerminal->name }}</p>
                                        </div>
                                        <div class="flex flex-col items-center px-4">
                                            <span class="text-sm text-gray-400">{{ $schedule->route->formatted_duration }}</span>
                                            <div class="w-20 h-0.5 bg-gray-300 my-1"></div>
                                            <span class="text-xs text-gray-400">{{ $schedule->route->distance_km }} km</span>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-gray-800">{{ $schedule->arrival_time->format('H:i') }}</p>
                                            <p class="text-sm text-gray-500">{{ $schedule->route->destinationTerminal->name }}</p>
                                        </div>
                                    </div>

                                    <!-- Price & Availability -->
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-indigo-600">{{ $schedule->formatted_price }}</p>
                                            <p class="text-sm text-gray-500">{{ $schedule->available_seats }} kursi tersedia</p>
                                        </div>
                                        <a href="{{ route('schedules.show', $schedule) }}"
                                            class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                                            Pilih
                                        </a>
                                    </div>
                                </div>

                                <!-- Amenities -->
                                @if($schedule->bus->busClass->amenities)
                                    <div class="mt-4 pt-4 border-t">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($schedule->bus->busClass->amenities as $amenity)
                                                <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                                    {{ $amenity }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
