<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('super-admin.terminals.index') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                ← {{ __('Back') }}
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Terminal') }}: {{ $terminal->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('super-admin.terminals.update', $terminal) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('Terminal Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $terminal->name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">
                                {{ __('Code') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code', $terminal->code) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   maxlength="20">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @php
                            $citiesByProvince = $provinces->keyBy('id')->map(fn($p) => $p->cities)->toArray();
                            $currentProvinceId = old('province_id', $terminal->city->province->id ?? '');
                        @endphp

                        <div x-data="{ 
                            provinceId: '{{ $currentProvinceId }}', 
                            cityId: '{{ old('city_id', $terminal->city_id) }}',
                            cities: @js($citiesByProvince[$currentProvinceId] ?? [])
                        }">
                            <label for="province_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Province') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="province_id" id="province_id" 
                                    x-model="provinceId"
                                    @change="cities = @js($citiesByProvince)[provinceId] || []; cityId = '';"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select Province') }}</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" {{ $currentProvinceId == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>

                            <label for="city_id" class="block text-sm font-medium text-gray-700 mt-4">
                                {{ __('City') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="city_id" id="city_id" required x-model="cityId"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select City') }}</option>
                                <template x-for="city in cities" :key="city.id">
                                    <option :value="city.id" x-text="city.name" :selected="city.id == cityId"></option>
                                </template>
                            </select>
                            @error('city_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">
                                {{ __('Address') }}
                            </label>
                            <textarea name="address" id="address" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $terminal->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $terminal->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                {{ __('Active') }}
                            </label>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <a href="{{ route('super-admin.terminals.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                {{ __('Update Terminal') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
