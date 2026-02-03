<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tiket Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Upcoming Tickets -->
            <div class="mb-8">
                <h3 class="font-bold text-lg mb-4">Tiket Aktif</h3>

                @if($upcomingTickets->isEmpty())
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <p class="text-gray-500">Tidak ada tiket aktif.</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                            Cari Tiket →
                        </a>
                    </div>
                @else
                    <div class="grid gap-4">
                        @foreach($upcomingTickets as $ticket)
                            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition">
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                        <!-- Booking Code -->
                                        <div class="mb-4 lg:mb-0">
                                            <p class="text-xs text-gray-500">Kode Booking</p>
                                            <p class="font-mono font-bold text-lg">{{ $ticket->booking_code }}</p>
                                            <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded mt-1">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </div>

                                        <!-- Route & Time -->
                                        <div class="mb-4 lg:mb-0">
                                            <p class="font-medium">
                                                {{ $ticket->schedule->route->originTerminal->name }} → {{ $ticket->schedule->route->destinationTerminal->name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $ticket->schedule->departure_time->format('d M Y, H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $ticket->schedule->busOperator->name }} - {{ $ticket->schedule->bus->busClass->name }}
                                            </p>
                                        </div>

                                        <!-- Passenger & Seat -->
                                        <div class="mb-4 lg:mb-0">
                                            <p class="text-sm text-gray-500">Penumpang</p>
                                            <p class="font-medium">{{ $ticket->passenger_name }}</p>
                                            <p class="text-sm text-gray-600">Kursi: {{ $ticket->seat_number }}</p>
                                        </div>

                                        <!-- Action -->
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition text-center">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Past Tickets -->
            @if($pastTickets->isNotEmpty())
                <div>
                    <h3 class="font-bold text-lg mb-4">Riwayat Perjalanan</h3>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rute</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pastTickets as $ticket)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $ticket->booking_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $ticket->schedule->route->originTerminal->name }} → {{ $ticket->schedule->route->destinationTerminal->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $ticket->schedule->departure_time->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded
                                                {{ $ticket->status === 'used' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
