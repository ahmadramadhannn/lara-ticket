<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Tiket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Ticket Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm opacity-80">Kode Booking</p>
                            <p class="text-3xl font-mono font-bold">{{ $ticket->booking_code }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $ticket->status === 'confirmed' ? 'bg-green-500' : ($ticket->status === 'used' ? 'bg-gray-500' : 'bg-yellow-500') }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    @php $schedule = $ticket->schedule; @endphp

                    <!-- Route Info -->
                    <div class="flex items-center justify-between border-b pb-6 mb-6">
                        <div class="text-center flex-1">
                            <p class="text-2xl font-bold">{{ $schedule->departure_time->format('H:i') }}</p>
                            <p class="font-medium">{{ $schedule->route->originTerminal->name }}</p>
                            <p class="text-sm text-gray-500">{{ $schedule->route->originTerminal->city->name }}</p>
                        </div>
                        <div class="flex flex-col items-center px-4">
                            <p class="text-sm text-gray-400">{{ $schedule->route->formatted_duration }}</p>
                            <div class="w-16 h-0.5 bg-gray-300 my-2"></div>
                            <p class="text-xs text-gray-400">{{ $schedule->route->distance_km }} km</p>
                        </div>
                        <div class="text-center flex-1">
                            <p class="text-2xl font-bold">{{ $schedule->arrival_time->format('H:i') }}</p>
                            <p class="font-medium">{{ $schedule->route->destinationTerminal->name }}</p>
                            <p class="text-sm text-gray-500">{{ $schedule->route->destinationTerminal->city->name }}</p>
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Keberangkatan</p>
                            <p class="font-medium">{{ $schedule->departure_time->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">PO Bus</p>
                            <p class="font-medium">{{ $schedule->busOperator->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kelas</p>
                            <p class="font-medium">{{ $schedule->bus->busClass->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">No. Bus</p>
                            <p class="font-medium">{{ $schedule->bus->registration_number }}</p>
                        </div>
                    </div>

                    <!-- Passenger & Seat -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Nama Penumpang</p>
                                <p class="text-lg font-bold">{{ $ticket->passenger_name }}</p>
                                @if($ticket->passenger_id_number)
                                    <p class="text-sm text-gray-600">KTP: {{ $ticket->passenger_id_number }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Nomor Kursi</p>
                                <p class="text-4xl font-bold text-indigo-600">{{ $ticket->seat_number }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Placeholder -->
                    <div class="border-t pt-6">
                        <div class="text-center">
                            <div class="inline-block bg-white p-4 border-2 border-dashed border-gray-300 rounded-lg">
                                <div class="w-40 h-40 bg-gray-100 flex items-center justify-center">
                                    <div class="text-center">
                                        <p class="text-4xl mb-2">📱</p>
                                        <p class="text-xs text-gray-500">QR Code</p>
                                        <p class="text-xs font-mono">{{ $ticket->booking_code }}</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                Tunjukkan kode ini kepada petugas terminal
                            </p>
                        </div>
                    </div>

                    <!-- Verified Info -->
                    @if($ticket->verified_at)
                        <div class="mt-6 bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-green-800">
                                ✓ Diverifikasi pada {{ $ticket->verified_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    @endif

                    <!-- Actions -->
                    @if($ticket->payment)
                        <div class="mt-6 flex justify-center space-x-4">
                            <a href="{{ route('payments.receipt', $ticket->payment) }}"
                                class="px-4 py-2 bg-white text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50 transition">
                                Lihat Struk
                            </a>
                            <a href="{{ route('tickets.download', $ticket) }}"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Download PDF
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back to Tickets -->
            <div class="mt-6 text-center">
                <a href="{{ route('tickets.index') }}" class="text-indigo-600 hover:text-indigo-800">
                    ← Kembali ke Tiket Saya
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
