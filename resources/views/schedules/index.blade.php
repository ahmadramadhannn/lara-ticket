<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cari Tiket Bus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-xl overflow-hidden mb-8">
                <div class="px-6 py-12 md:px-12 text-center">
                    <h1 class="text-4xl font-bold text-white mb-4">
                        Pesan Tiket Bus Online
                    </h1>
                    <p class="text-indigo-100 text-lg mb-8">
                        Perjalanan nyaman dengan harga terbaik
                    </p>
                </div>
            </div>

            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('schedules.search') }}" method="GET" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Origin -->
                        <div>
                            <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">
                                Dari
                            </label>
                            <select id="origin" name="origin" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Terminal Asal</option>
                                @foreach($terminals as $province => $provinceTerminals)
                                    <optgroup label="{{ $province }}">
                                        @foreach($provinceTerminals as $terminal)
                                            <option value="{{ $terminal->id }}">
                                                {{ $terminal->name }} ({{ $terminal->city->name }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Destination -->
                        <div>
                            <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">
                                Ke
                            </label>
                            <select id="destination" name="destination" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Terminal Tujuan</option>
                                @foreach($terminals as $province => $provinceTerminals)
                                    <optgroup label="{{ $province }}">
                                        @foreach($provinceTerminals as $terminal)
                                            <option value="{{ $terminal->id }}">
                                                {{ $terminal->name }} ({{ $terminal->city->name }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Berangkat
                            </label>
                            <input type="date" id="date" name="date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ date('Y-m-d') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Operator (optional) -->
                        <div>
                            <label for="operator" class="block text-sm font-medium text-gray-700 mb-1">
                                PO Bus (opsional)
                            </label>
                            <select id="operator" name="operator"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua PO</option>
                                @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            🔍 Cari Jadwal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Features -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-4xl mb-4">🎫</div>
                    <h3 class="font-semibold text-lg mb-2">E-Ticket</h3>
                    <p class="text-gray-600 text-sm">Tiket digital dengan QR code, tidak perlu cetak</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-4xl mb-4">🔒</div>
                    <h3 class="font-semibold text-lg mb-2">Aman & Terpercaya</h3>
                    <p class="text-gray-600 text-sm">Pembayaran terjamin dengan berbagai metode</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-4xl mb-4">🚌</div>
                    <h3 class="font-semibold text-lg mb-2">Banyak Pilihan</h3>
                    <p class="text-gray-600 text-sm">Berbagai PO bus terbaik di Indonesia</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
