<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pilih Kursi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Schedule Details -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                        <h3 class="font-bold text-lg mb-4">Detail Jadwal</h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Operator</p>
                                <p class="font-medium">{{ $schedule->busOperator->name }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Kelas</p>
                                <p class="font-medium">{{ $schedule->bus->busClass->name }}</p>
                            </div>

                            <div class="border-t pt-4">
                                <p class="text-sm text-gray-500">Rute</p>
                                <p class="font-medium">{{ $schedule->route->originTerminal->name }}</p>
                                <p class="text-xs text-gray-400">{{ $schedule->route->originTerminal->city->name }}</p>
                                <div class="text-center my-2">↓</div>
                                <p class="font-medium">{{ $schedule->route->destinationTerminal->name }}</p>
                                <p class="text-xs text-gray-400">{{ $schedule->route->destinationTerminal->city->name }}</p>
                            </div>

                            <div class="border-t pt-4">
                                <p class="text-sm text-gray-500">Waktu</p>
                                <p class="font-medium">{{ $schedule->departure_time->format('d M Y') }}</p>
                                <p class="text-sm">{{ $schedule->departure_time->format('H:i') }} - {{ $schedule->arrival_time->format('H:i') }}</p>
                                <p class="text-xs text-gray-400">~{{ $schedule->route->formatted_duration }}</p>
                            </div>

                            <div class="border-t pt-4">
                                <p class="text-sm text-gray-500">Harga per Kursi</p>
                                <p class="text-2xl font-bold text-indigo-600">{{ $schedule->formatted_price }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seat Selection -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-bold text-lg mb-4">Pilih Kursi</h3>

                        <!-- Legend -->
                        <div class="flex space-x-4 mb-6">
                            <div class="flex items-center">
                                <span class="w-6 h-6 bg-gray-200 rounded mr-2"></span>
                                <span class="text-sm">Tersedia</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-6 h-6 bg-red-400 rounded mr-2"></span>
                                <span class="text-sm">Terisi</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-6 h-6 bg-indigo-600 rounded mr-2"></span>
                                <span class="text-sm">Dipilih</span>
                            </div>
                        </div>

                        <!-- Seat Layout -->
                        <div class="bg-gray-50 rounded-lg p-6" x-data="{ selectedSeat: null }">
                            <!-- Driver Area -->
                            <div class="text-center mb-6 pb-4 border-b-2 border-dashed border-gray-300">
                                <span class="text-sm text-gray-500">🚌 Depan Bus</span>
                            </div>

                            @if(!empty($seatLayout))
                                <div class="space-y-2 max-w-md mx-auto">
                                    @foreach($seatLayout as $row)
                                        <div class="flex justify-center space-x-2">
                                            @foreach($row as $index => $seat)
                                                @php
                                                    $isBooked = in_array($seat, $bookedSeats);
                                                @endphp

                                                @if($index == 2 && count($row) == 4)
                                                    <div class="w-10"></div> <!-- Aisle -->
                                                @elseif($index == 1 && count($row) == 3)
                                                    <div class="w-10"></div> <!-- Aisle -->
                                                @endif

                                                <button
                                                    type="button"
                                                    @click="!{{ $isBooked ? 'true' : 'false' }} && (selectedSeat = '{{ $seat }}')"
                                                    :class="{
                                                        'bg-indigo-600 text-white': selectedSeat === '{{ $seat }}',
                                                        'bg-red-400 cursor-not-allowed': {{ $isBooked ? 'true' : 'false' }},
                                                        'bg-gray-200 hover:bg-gray-300': selectedSeat !== '{{ $seat }}' && !{{ $isBooked ? 'true' : 'false' }}
                                                    }"
                                                    class="w-10 h-10 rounded font-medium text-sm transition"
                                                    {{ $isBooked ? 'disabled' : '' }}
                                                >
                                                    {{ $seat }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Fallback simple layout -->
                                <div class="grid grid-cols-4 gap-2 max-w-md mx-auto">
                                    @for($i = 1; $i <= $schedule->bus->total_seats; $i++)
                                        @php
                                            $seat = (string) $i;
                                            $isBooked = in_array($seat, $bookedSeats);
                                        @endphp
                                        <button
                                            type="button"
                                            @click="!{{ $isBooked ? 'true' : 'false' }} && (selectedSeat = '{{ $seat }}')"
                                            :class="{
                                                'bg-indigo-600 text-white': selectedSeat === '{{ $seat }}',
                                                'bg-red-400 cursor-not-allowed': {{ $isBooked ? 'true' : 'false' }},
                                                'bg-gray-200 hover:bg-gray-300': selectedSeat !== '{{ $seat }}' && !{{ $isBooked ? 'true' : 'false' }}
                                            }"
                                            class="w-10 h-10 rounded font-medium text-sm transition"
                                            {{ $isBooked ? 'disabled' : '' }}
                                        >
                                            {{ $seat }}
                                        </button>
                                    @endfor
                                </div>
                            @endif

                            <!-- Back of bus -->
                            <div class="text-center mt-6 pt-4 border-t-2 border-dashed border-gray-300">
                                <span class="text-sm text-gray-500">Belakang Bus</span>
                            </div>

                            <!-- Selected Seat Info & Proceed -->
                            <div class="mt-6 pt-6 border-t" x-show="selectedSeat" x-transition>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Kursi Dipilih</p>
                                        <p class="text-xl font-bold" x-text="selectedSeat"></p>
                                    </div>
                                    @auth
                                        <form :action="'{{ route('booking.create', $schedule) }}?seat=' + selectedSeat" method="GET">
                                            <input type="hidden" name="seat" x-model="selectedSeat">
                                            <button type="submit"
                                                class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                                                Lanjut Pemesanan
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                                            Login untuk Pesan
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
