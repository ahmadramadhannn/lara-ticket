<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menunggu Persetujuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        {{ __('Pendaftaran Anda Sedang Diproses') }}
                    </h3>
                    
                    <p class="text-gray-600 mb-6">
                        {{ __('Terima kasih telah mendaftar sebagai operator PO. Pendaftaran Anda sedang menunggu persetujuan dari admin. Anda akan mendapatkan notifikasi setelah pendaftaran disetujui.') }}
                    </p>

                    @if(auth()->user()->busOperator)
                    <div class="bg-gray-50 rounded-lg p-4 text-left mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">{{ __('Detail Pendaftaran:') }}</h4>
                        <dl class="grid grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500">{{ __('Nama PO:') }}</dt>
                            <dd class="text-gray-900">{{ auth()->user()->busOperator->name }}</dd>
                            <dt class="text-gray-500">{{ __('Kode PO:') }}</dt>
                            <dd class="text-gray-900">{{ auth()->user()->busOperator->code }}</dd>
                            <dt class="text-gray-500">{{ __('Status:') }}</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ __('Menunggu Persetujuan') }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                    @endif

                    <div class="space-x-4">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali ke Beranda') }}
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
