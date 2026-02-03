<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Struk Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden" id="receipt">
                @php
                    $ticket = $payment->payable;
                    $schedule = $ticket->schedule;
                @endphp

                <!-- Header -->
                <div class="bg-gray-800 text-white p-6 text-center">
                    <h1 class="text-2xl font-bold">STRUK PEMBAYARAN</h1>
                    <p class="text-gray-400 text-sm mt-1">{{ config('app.name') }}</p>
                </div>

                <div class="p-6">
                    <!-- Invoice Details -->
                    <div class="border-b pb-4 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">No. Invoice</span>
                            <span class="font-mono font-bold">{{ $payment->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500">Tanggal Pembayaran</span>
                            <span>{{ $payment->paid_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500">Metode</span>
                            <span>{{ str_replace('_', ' ', ucfirst($payment->method)) }}</span>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="border-b pb-4 mb-4">
                        <h3 class="font-bold text-sm text-gray-800 mb-2">DATA PEMBELI</h3>
                        <p class="text-sm">{{ $payment->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $payment->user->email }}</p>
                    </div>

                    <!-- Ticket Details -->
                    <div class="border-b pb-4 mb-4">
                        <h3 class="font-bold text-sm text-gray-800 mb-2">DETAIL TIKET</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kode Booking</span>
                                <span class="font-mono font-bold">{{ $ticket->booking_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Penumpang</span>
                                <span>{{ $ticket->passenger_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">PO Bus</span>
                                <span>{{ $schedule->busOperator->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kelas</span>
                                <span>{{ $schedule->bus->busClass->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Rute</span>
                                <span class="text-right">
                                    {{ $schedule->route->originTerminal->name }}<br>
                                    ↓<br>
                                    {{ $schedule->route->destinationTerminal->name }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Keberangkatan</span>
                                <span>{{ $schedule->departure_time->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kursi</span>
                                <span class="font-bold">{{ $ticket->seat_number }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>TOTAL DIBAYAR</span>
                        <span class="text-indigo-600">{{ $payment->formatted_amount }}</span>
                    </div>

                    <!-- Status -->
                    <div class="mt-6 text-center">
                        <span class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            ✓ LUNAS
                        </span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 p-4 text-center text-xs text-gray-500">
                    <p>Terima kasih telah menggunakan layanan kami.</p>
                    <p class="mt-1">Simpan struk ini sebagai bukti pembayaran.</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-center space-x-4 mt-6">
                <a href="{{ route('tickets.show', $ticket) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Lihat Tiket
                </a>
                <button onclick="window.print()"
                    class="px-4 py-2 bg-white text-gray-700 border rounded-lg hover:bg-gray-50 transition">
                    🖨️ Cetak
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
