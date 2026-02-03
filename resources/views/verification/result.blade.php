<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Verifikasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Validation Result -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="p-8 text-center
                    {{ $validation['type'] === 'success' ? 'bg-green-500' : ($validation['type'] === 'warning' ? 'bg-yellow-500' : 'bg-red-500') }}">
                    <div class="text-6xl mb-4">
                        @if($validation['type'] === 'success')
                            ✅
                        @elseif($validation['type'] === 'warning')
                            ⚠️
                        @else
                            ❌
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">
                        {{ $validation['valid'] ? 'TIKET VALID' : 'TIKET TIDAK VALID' }}
                    </h1>
                    <p class="text-white opacity-90">{{ $validation['message'] }}</p>
                </div>

                @php $schedule = $ticket->schedule; @endphp

                <div class="p-6">
                    <!-- Ticket Info -->
                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-4">
                            <span class="text-gray-500">Kode Booking</span>
                            <span class="font-mono font-bold text-lg">{{ $ticket->booking_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nama Penumpang</span>
                            <span class="font-bold">{{ $ticket->passenger_name }}</span>
                        </div>
                        @if($ticket->passenger_id_number)
                            <div class="flex justify-between">
                                <span class="text-gray-500">No. KTP</span>
                                <span>{{ $ticket->passenger_id_number }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kursi</span>
                            <span class="font-bold text-indigo-600 text-xl">{{ $ticket->seat_number }}</span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Rute</span>
                                <span class="text-right">
                                    {{ $schedule->route->originTerminal->name }} → {{ $schedule->route->destinationTerminal->name }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Keberangkatan</span>
                            <span>{{ $schedule->departure_time->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">PO Bus</span>
                            <span>{{ $schedule->busOperator->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kelas</span>
                            <span>{{ $schedule->bus->busClass->name }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($validation['valid'] && $ticket->status === 'confirmed')
                        <div class="mt-6 pt-6 border-t">
                            <form action="{{ route('ticket-check.markUsed', $ticket) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
                                    onclick="return confirm('Konfirmasi penumpang naik bus?')">
                                    ✓ Konfirmasi Naik Bus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back to Verification -->
            <div class="text-center">
                <a href="{{ route('ticket-check.index') }}"
                    class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition inline-block">
                    Verifikasi Tiket Lain
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
