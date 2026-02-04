<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-xl overflow-hidden mb-8">
                <div class="px-6 py-12 md:px-12 text-center">
                    <h1 class="text-4xl font-bold text-white mb-4">
                        {{ __('Book Online Bus Tickets') }}
                    </h1>
                    <p class="text-indigo-100 text-lg mb-8">
                        {{ __('Comfortable travel at the best price') }}
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
                                {{ __('From') }}
                            </label>
                            <select id="origin" name="origin" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Origin Terminal') }}</option>
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
                                {{ __('To') }}
                            </label>
                            <select id="destination" name="destination" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Destination Terminal') }}</option>
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
                                {{ __('Departure Date') }}
                            </label>
                            <input type="date" id="date" name="date" required
                                min="{{ date('Y-m-d') }}"
                                value="{{ date('Y-m-d') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Operator (optional) -->
                        <div>
                            <label for="operator" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Bus Operator (optional)') }}
                            </label>
                            <select id="operator" name="operator"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Operators') }}</option>
                                @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            🔍 {{ __('Search Schedule') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Features -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-4xl mb-4">🎫</div>
                    <h3 class="font-semibold text-lg mb-2">{{ __('E-Ticket') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('Digital ticket with QR code, no need to print') }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-4xl mb-4">🔒</div>
                    <h3 class="font-semibold text-lg mb-2">{{ __('Safe & Trusted') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('Secure payment with various methods') }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-4xl mb-4">🚌</div>
                    <h3 class="font-semibold text-lg mb-2">{{ __('Many Choices') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('Various best bus operators in Indonesia') }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const originSelect = document.getElementById('origin');
            const destinationSelect = document.getElementById('destination');
            const originalDestinations = destinationSelect.innerHTML;
            
            originSelect.addEventListener('change', async function() {
                const originId = this.value;
                
                if (!originId) {
                    // Reset to all destinations
                    destinationSelect.innerHTML = originalDestinations;
                    return;
                }
                
                // Show loading state
                destinationSelect.innerHTML = '<option value="">{{ __("Loading...") }}</option>';
                destinationSelect.disabled = true;
                
                try {
                    const response = await fetch(`/api/destinations/${originId}`);
                    const data = await response.json();
                    
                    let html = '<option value="">{{ __("Select Destination Terminal") }}</option>';
                    
                    if (data.length === 0) {
                        html = '<option value="">{{ __("No destinations available") }}</option>';
                    } else {
                        data.forEach(group => {
                            html += `<optgroup label="${group.province}">`;
                            group.terminals.forEach(terminal => {
                                html += `<option value="${terminal.id}">${terminal.name} (${terminal.city})</option>`;
                            });
                            html += '</optgroup>';
                        });
                    }
                    
                    destinationSelect.innerHTML = html;
                } catch (error) {
                    console.error('Error fetching destinations:', error);
                    destinationSelect.innerHTML = originalDestinations;
                } finally {
                    destinationSelect.disabled = false;
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
