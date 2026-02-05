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

            <!-- Two Column Layout: Sidebar + Results -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left Sidebar: Filters -->
                <aside class="lg:w-72 flex-shrink-0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 sticky top-6">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2">🔍</span> {{ __('Filter Results') }}
                        </h3>
                        
                        <form method="GET" action="{{ route('schedules.search') }}">
                            <!-- Preserve search parameters -->
                            <input type="hidden" name="origin" value="{{ $origin->id }}">
                            <input type="hidden" name="destination" value="{{ $destination->id }}">
                            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">

                            <!-- Price Range -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Price Range') }}</label>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 w-12">Min</span>
                                        <input type="number" 
                                               name="min_price" 
                                               value="{{ $activeFilters['min_price'] ?? '' }}"
                                               placeholder="0"
                                               min="0"
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 w-12">Max</span>
                                        <input type="number" 
                                               name="max_price" 
                                               value="{{ $activeFilters['max_price'] ?? '' }}"
                                               placeholder="∞"
                                               min="0"
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Bus Class -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Bus Class') }}</label>
                                <div class="space-y-2">
                                    @foreach($busClasses as $busClass)
                                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-1.5 rounded -mx-1.5">
                                            <input type="checkbox" 
                                                   name="bus_class[]" 
                                                   value="{{ $busClass->id }}"
                                                   {{ in_array($busClass->id, $activeFilters['bus_class'] ?? []) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-3 text-sm text-gray-700">{{ $busClass->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <button type="submit"
                                        class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                    {{ __('Apply Filters') }}
                                </button>
                                <a href="{{ route('schedules.search', ['origin' => $origin->id, 'destination' => $destination->id, 'date' => $date->format('Y-m-d')]) }}"
                                   class="block w-full px-4 py-2 text-center text-gray-600 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                    {{ __('Clear Filters') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </aside>

                <!-- Right Column: Results -->
                <main class="flex-1 min-w-0">
                    @if($schedules->isEmpty())
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-yellow-700">
                                Tidak ada jadwal tersedia untuk rute dan tanggal yang dipilih.
                            </p>
                        </div>
                    @else
                        <div class="mb-4 text-sm text-gray-600">
                            {{ __('Showing :count results', ['count' => $schedules->count()]) }}
                        </div>
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
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
