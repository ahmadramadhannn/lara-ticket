<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('super-admin.routes.index') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                ← {{ __('Back') }}
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Route') }}
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
                    <form action="{{ route('super-admin.routes.update', $route) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="origin_terminal_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Origin Terminal') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="origin_terminal_id" id="origin_terminal_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Origin Terminal') }}</option>
                                @foreach($terminals as $province => $terminalGroup)
                                    <optgroup label="{{ $province }}">
                                        @foreach($terminalGroup as $terminal)
                                            <option value="{{ $terminal->id }}" 
                                                {{ old('origin_terminal_id', $route->origin_terminal_id) == $terminal->id ? 'selected' : '' }}>
                                                {{ $terminal->name }} ({{ $terminal->city->name }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('origin_terminal_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="destination_terminal_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Destination Terminal') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="destination_terminal_id" id="destination_terminal_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Destination Terminal') }}</option>
                                @foreach($terminals as $province => $terminalGroup)
                                    <optgroup label="{{ $province }}">
                                        @foreach($terminalGroup as $terminal)
                                            <option value="{{ $terminal->id }}" 
                                                {{ old('destination_terminal_id', $route->destination_terminal_id) == $terminal->id ? 'selected' : '' }}>
                                                {{ $terminal->name }} ({{ $terminal->city->name }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('destination_terminal_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="distance_km" class="block text-sm font-medium text-gray-700">
                                    {{ __('Distance (km)') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="distance_km" id="distance_km" 
                                       value="{{ old('distance_km', $route->distance_km) }}" 
                                       required min="1" max="9999" step="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('distance_km')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700">
                                    {{ __('Duration (minutes)') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="estimated_duration_minutes" id="estimated_duration_minutes" 
                                       value="{{ old('estimated_duration_minutes', $route->estimated_duration_minutes) }}" 
                                       required min="1" max="2880" step="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('estimated_duration_minutes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $route->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                {{ __('Active') }}
                            </label>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <a href="{{ route('super-admin.routes.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                {{ __('Update Route') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
