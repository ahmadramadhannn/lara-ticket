<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Payment Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                    <p class="text-sm opacity-80">Invoice Number</p>
                    <p class="text-xl font-mono font-bold">{{ $payment->invoice_number }}</p>
                </div>

                <div class="p-6">
                    @php
                        $ticket = $payment->payable;
                        $schedule = $ticket->schedule;
                    @endphp

                    <!-- Ticket Details -->
                    <div class="border-b pb-6 mb-6">
                        <h3 class="font-bold text-lg mb-4">Detail Tiket</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Kode Booking</p>
                                <p class="font-mono font-bold text-lg">{{ $ticket->booking_code }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Status</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded
                                    {{ $ticket->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-500">Rute</p>
                                <p class="font-medium">{{ $schedule->route->originTerminal->name }} → {{ $schedule->route->destinationTerminal->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Keberangkatan</p>
                                <p class="font-medium">{{ $schedule->departure_time->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Penumpang</p>
                                <p class="font-medium">{{ $ticket->passenger_name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Kursi</p>
                                <p class="font-medium">{{ $ticket->seat_number }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">PO Bus</p>
                                <p class="font-medium">{{ $schedule->busOperator->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    @if($payment->isPaid())
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                            <div class="text-4xl mb-2">✅</div>
                            <h3 class="font-bold text-lg text-green-800">Pembayaran Berhasil!</h3>
                            <p class="text-green-600 text-sm">Dibayar pada {{ $payment->paid_at->format('d M Y, H:i') }}</p>
                            <div class="mt-4 flex justify-center space-x-4">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Lihat Tiket
                                </a>
                                <a href="{{ route('payments.receipt', $payment) }}"
                                    class="px-4 py-2 bg-white text-green-600 border border-green-600 rounded-lg hover:bg-green-50 transition">
                                    Lihat Struk
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Amount -->
                        <div class="border-b pb-6 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Pembayaran</span>
                                <span class="text-3xl font-bold text-indigo-600">{{ $payment->formatted_amount }}</span>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <form action="{{ route('payments.process', $payment) }}" method="POST"
                            x-data="{ method: null }">
                            @csrf
                            <h3 class="font-bold text-lg mb-4">Pilih Metode Pembayaran</h3>

                            <div class="space-y-3 mb-6">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition"
                                    :class="{ 'border-indigo-600 bg-indigo-50': method === 'bank_transfer', 'hover:border-gray-400': method !== 'bank_transfer' }">
                                    <input type="radio" name="method" value="bank_transfer" x-model="method" class="sr-only">
                                    <span class="text-2xl mr-4">🏦</span>
                                    <div>
                                        <p class="font-medium">Transfer Bank</p>
                                        <p class="text-sm text-gray-500">BCA, Mandiri, BNI, BRI</p>
                                    </div>
                                    <span class="ml-auto" x-show="method === 'bank_transfer'">✓</span>
                                </label>

                                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition"
                                    :class="{ 'border-indigo-600 bg-indigo-50': method === 'e_wallet', 'hover:border-gray-400': method !== 'e_wallet' }">
                                    <input type="radio" name="method" value="e_wallet" x-model="method" class="sr-only">
                                    <span class="text-2xl mr-4">📱</span>
                                    <div>
                                        <p class="font-medium">E-Wallet</p>
                                        <p class="text-sm text-gray-500">GoPay, OVO, Dana, ShopeePay</p>
                                    </div>
                                    <span class="ml-auto" x-show="method === 'e_wallet'">✓</span>
                                </label>

                                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition"
                                    :class="{ 'border-indigo-600 bg-indigo-50': method === 'credit_card', 'hover:border-gray-400': method !== 'credit_card' }">
                                    <input type="radio" name="method" value="credit_card" x-model="method" class="sr-only">
                                    <span class="text-2xl mr-4">💳</span>
                                    <div>
                                        <p class="font-medium">Kartu Kredit/Debit</p>
                                        <p class="text-sm text-gray-500">Visa, Mastercard</p>
                                    </div>
                                    <span class="ml-auto" x-show="method === 'credit_card'">✓</span>
                                </label>
                            </div>

                            @error('method')
                                <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
                            @enderror

                            <button type="submit"
                                :disabled="!method"
                                :class="{ 'opacity-50 cursor-not-allowed': !method }"
                                class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                                Bayar Sekarang
                            </button>

                            <p class="text-center text-xs text-gray-500 mt-4">
                                Ini adalah demo. Pembayaran akan langsung berhasil.
                            </p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
