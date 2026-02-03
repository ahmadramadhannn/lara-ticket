<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('operator.buses.index') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                ← {{ __('Back') }}
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Bus') }}: {{ $bus->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('operator.buses.update', $bus) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('Bus Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $bus->name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700">
                                {{ __('Registration Number') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="registration_number" id="registration_number" 
                                   value="{{ old('registration_number', $bus->registration_number) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('registration_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bus_class_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Bus Class') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="bus_class_id" id="bus_class_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Class') }}</option>
                                @foreach($busClasses as $class)
                                    <option value="{{ $class->id }}" 
                                        {{ old('bus_class_id', $bus->bus_class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bus_class_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="total_seats" class="block text-sm font-medium text-gray-700">
                                {{ __('Total Seats') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="total_seats" id="total_seats" 
                                   value="{{ old('total_seats', $bus->total_seats) }}" required min="1" max="100"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('total_seats')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <a href="{{ route('operator.buses.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                {{ __('Update Bus') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
