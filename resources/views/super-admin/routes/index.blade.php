<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Routes') }}
            </h2>
            <a href="{{ route('super-admin.routes.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                + {{ __('Add Route') }}
            </a>
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

            <!-- Search & Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('super-admin.routes.index') }}" method="GET" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="{{ __('Search by terminal name or code...') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="w-48">
                            <select name="origin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Origins') }}</option>
                                @foreach($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ request('origin') == $terminal->id ? 'selected' : '' }}>
                                        {{ $terminal->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-48">
                            <select name="destination" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Destinations') }}</option>
                                @foreach($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ request('destination') == $terminal->id ? 'selected' : '' }}>
                                        {{ $terminal->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-36">
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'origin', 'destination', 'status']))
                            <a href="{{ route('super-admin.routes.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Routes Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($routes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Origin') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Destination') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Distance') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Duration') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($routes as $route)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $route->originTerminal->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $route->originTerminal->city->name ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-400">
                                                →
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $route->destinationTerminal->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $route->destinationTerminal->city->name ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $route->distance_km }} km
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $hours = floor($route->estimated_duration_minutes / 60);
                                                    $mins = $route->estimated_duration_minutes % 60;
                                                @endphp
                                                {{ $hours }}{{ __('h') }} {{ $mins }}{{ __('m') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($route->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ __('Active') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ __('Inactive') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                                <a href="{{ route('super-admin.routes.edit', $route) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                                <form action="{{ route('super-admin.routes.destroy', $route) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this route?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $routes->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No routes found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Get started by creating a new route.') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('super-admin.routes.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    + {{ __('Add Route') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
