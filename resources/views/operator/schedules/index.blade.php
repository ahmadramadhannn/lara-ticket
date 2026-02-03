<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Schedules') }} - {{ $operator->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('operator.schedules.create') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    + {{ __('Create Schedule') }}
                </a>
                <button onclick="document.getElementById('bulk-modal').classList.remove('hidden')"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    📅 {{ __('Bulk Create') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('operator.schedules.index') }}" method="GET" class="flex flex-wrap gap-4">
                        <div class="w-48">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('Date') }}</label>
                            <input type="date" name="date" value="{{ request('date') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="w-56">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('Route') }}</label>
                            <select name="route" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Routes') }}</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ request('route') == $route->id ? 'selected' : '' }}>
                                        {{ $route->originTerminal->name }} → {{ $route->destinationTerminal->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-40">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('Bus') }}</label>
                            <select name="bus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Buses') }}</option>
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" {{ request('bus') == $bus->id ? 'selected' : '' }}>
                                        {{ $bus->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-36">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('Status') }}</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                                {{ __('Filter') }}
                            </button>
                            @if(request()->hasAny(['date', 'route', 'bus', 'status']))
                                <a href="{{ route('operator.schedules.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Schedules Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($schedules->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date/Time') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Route') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Bus') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Seats') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($schedules as $schedule)
                                        <tr class="{{ $schedule->departure_time < now() ? 'bg-gray-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $schedule->departure_time->format('d M Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $schedule->departure_time->format('H:i') }} - {{ $schedule->arrival_time->format('H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $schedule->route->originTerminal->name }}</div>
                                                <div class="text-xs text-gray-500">↓</div>
                                                <div class="text-sm text-gray-900">{{ $schedule->route->destinationTerminal->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $schedule->bus->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $schedule->bus->busClass->name ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($schedule->base_price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $schedule->available_seats }}/{{ $schedule->bus->total_seats }}</div>
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mt-1">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($schedule->available_seats / $schedule->bus->total_seats) * 100 }}%"></div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($schedule->status === 'scheduled')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ __('Scheduled') }}
                                                    </span>
                                                @elseif($schedule->status === 'cancelled')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ __('Cancelled') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $schedule->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                                @if($schedule->departure_time > now())
                                                    <a href="{{ route('operator.schedules.edit', $schedule) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                                    <form action="{{ route('operator.schedules.destroy', $schedule) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this schedule?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400">{{ __('Past') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $schedules->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No schedules found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Create your first schedule to start selling tickets.') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('operator.schedules.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    + {{ __('Create Schedule') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Create Modal -->
    <div id="bulk-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Bulk Create Schedules') }}</h3>
                <form action="{{ route('operator.schedules.bulk-create') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="bulk_route_id" class="block text-sm font-medium text-gray-700">{{ __('Route') }}</label>
                        <select name="route_id" id="bulk_route_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Select Route') }}</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">
                                    {{ $route->originTerminal->name }} → {{ $route->destinationTerminal->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="bulk_bus_id" class="block text-sm font-medium text-gray-700">{{ __('Bus') }}</label>
                        <select name="bus_id" id="bulk_bus_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Select Bus') }}</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->name }} ({{ $bus->busClass->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="bulk_departure_time" class="block text-sm font-medium text-gray-700">{{ __('Departure Time') }}</label>
                        <input type="time" name="departure_time" id="bulk_departure_time" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="bulk_base_price" class="block text-sm font-medium text-gray-700">{{ __('Base Price (Rp)') }}</label>
                        <input type="number" name="base_price" id="bulk_base_price" required min="1000" step="1000"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. 150000">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('From Date') }}</label>
                            <input type="date" name="date_from" id="date_from" required min="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('To Date') }}</label>
                            <input type="date" name="date_to" id="date_to" required min="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onclick="document.getElementById('bulk-modal').classList.add('hidden')"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                            {{ __('Create Schedules') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
