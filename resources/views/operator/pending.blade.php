<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900 mb-2">{{ __('Registration Pending') }}</h2>
                    <p class="text-gray-600 mb-4">{{ __('Thank you for registering') }}!</p>
                    <p class="text-gray-500 text-sm">
                        {{ __('Your registration is being reviewed') }}.<br>
                        {{ __('We will notify you via email') }}.
                    </p>

                    <div class="mt-6 border-t pt-6">
                        <h3 class="font-medium text-gray-900 mb-4">{{ __('What happens next?') }}</h3>
                        <div class="text-left space-y-3">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 h-5 w-5 text-indigo-600">1.</span>
                                <span class="ml-3 text-gray-600">{{ __('Our team reviews your data') }}</span>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 h-5 w-5 text-indigo-600">2.</span>
                                <span class="ml-3 text-gray-600">{{ __('You will receive email confirmation') }}</span>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 h-5 w-5 text-indigo-600">3.</span>
                                <span class="ml-3 text-gray-600">{{ __('After approval, access operator dashboard') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('Back to Home') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
