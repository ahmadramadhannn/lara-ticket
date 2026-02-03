<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Operator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center py-8">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            {{ __('Welcome') }}, {{ auth()->user()->name }}!
                        </h3>
                        <p class="text-gray-600 mb-4">
                            {{ __('You are registered as operator for') }}:
                        </p>
                        @if(auth()->user()->busOperator)
                        <p class="text-xl font-semibold text-indigo-600">
                            {{ auth()->user()->busOperator->name }} ({{ auth()->user()->busOperator->code }})
                        </p>
                        @endif
                    </div>

                    <div class="border-t pt-6 mt-6">
                        <h4 class="font-medium text-gray-900 mb-4">{{ __('Operator Menu') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('ticket-check.index') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ __('Verify Ticket') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('Scan QR Code') }}</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('operator.schedules.index') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ __('Manage Schedule') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('Create & Edit Schedules') }}</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('operator.buses.index') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ __('Manage Bus') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('Register & Edit Buses') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
