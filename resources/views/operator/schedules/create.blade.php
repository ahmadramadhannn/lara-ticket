<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('operator.schedules.index') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                ← {{ __('Back') }}
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Schedule') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('operator.schedules.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="route_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Route') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="route_id" id="route_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Route') }}</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                        {{ $route->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('route_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bus_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Bus') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="bus_id" id="bus_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Bus') }}</option>
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                                        {{ $bus->name }} - {{ $bus->busClass->name ?? '' }} ({{ $bus->total_seats }} {{ __('seats') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('bus_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="departure_date" class="block text-sm font-medium text-gray-700">
                                    {{ __('Departure Date') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="departure_date" id="departure_date" 
                                       value="{{ old('departure_date', date('Y-m-d')) }}" 
                                       required min="{{ date('Y-m-d') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('departure_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="departure_time" class="block text-sm font-medium text-gray-700">
                                    {{ __('Departure Time') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="departure_time" id="departure_time" 
                                       value="{{ old('departure_time', '08:00') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('departure_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">
                                {{ __('Base Price (Rp)') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="base_price" id="base_price" 
                                   value="{{ old('base_price') }}" required min="1000" step="1000"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g. 150000">
                            <p class="mt-1 text-sm text-gray-500">{{ __('Price per seat') }}</p>
                            @error('base_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <a href="{{ route('operator.schedules.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                {{ __('Create Schedule') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
