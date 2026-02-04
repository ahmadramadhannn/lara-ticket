<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ url('/super-admin') }}">
                        @elseif(Auth::user()->isOperator() && Auth::user()->hasApprovedOperator())
                            <a href="{{ url('/operator') }}">
                        @else
                            <a href="{{ route('home') }}">
                        @endif
                    @else
                        <a href="{{ route('home') }}">
                    @endauth
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    
                    @auth
                        @if(Auth::user()->isBuyer())
                            <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                                {{ __('Search Tickets') }}
                            </x-nav-link>
                            <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                                {{ __('My Tickets') }}
                            </x-nav-link>
                        @endif

                        @if(Auth::user()->isOperator() && Auth::user()->hasApprovedOperator())
                            <x-nav-link :href="url('/operator')" :active="request()->routeIs('operator.*')">
                                {{ __('Dashboard Operator') }}
                            </x-nav-link>
                            <x-nav-link :href="route('ticket-check.index')" :active="request()->routeIs('ticket-check.*')">
                                {{ __('Verify Ticket') }}
                            </x-nav-link>
                        @endif

                        @if(Auth::user()->isSuperAdmin())
                            <x-nav-link :href="url('/super-admin')" :active="request()->routeIs('super-admin.*')">
                                {{ __('Dashboard Admin') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            @auth
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center">
                                    {{ Auth::user()->name }}
                                    @if(Auth::user()->isOperator())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Operator
                                        </span>
                                    @elseif(Auth::user()->isSuperAdmin())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            Super Admin
                                        </span>
                                    @endif
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if(Auth::user()->isOperator() && Auth::user()->hasApprovedOperator())
                                <x-dropdown-link :href="url('/operator')">
                                    {{ __('Dashboard Operator') }}
                                </x-dropdown-link>
                            @endif

                            @if(Auth::user()->isSuperAdmin())
                                <x-dropdown-link :href="url('/super-admin')">
                                    {{ __('Dashboard Admin') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @else
                <!-- Guest Links -->
                <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                    <!-- Language Switcher -->
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                            <span class="mr-1">{{ app()->getLocale() === 'id' ? '🇮🇩' : '🇬🇧' }}</span>
                            <span>{{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}</span>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" class="absolute right-0 mt-2 w-24 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('language.switch', 'id') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'id' ? 'bg-gray-100' : '' }}">
                                🇮🇩 Indonesia
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'bg-gray-100' : '' }}">
                                🇬🇧 English
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">{{ __('Register') }}</a>
                    <a href="{{ route('operator.register') }}" class="px-4 py-2 border border-indigo-600 text-indigo-600 rounded-md hover:bg-indigo-50">{{ __('Register PO') }}</a>
                </div>
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endauth
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Search Tickets') }}
            </x-responsive-nav-link>
            
            @auth
                @if(Auth::user()->isBuyer())
                    <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                        {{ __('My Tickets') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->isOperator() && Auth::user()->hasApprovedOperator())
                    <x-responsive-nav-link :href="url('/operator')" :active="request()->routeIs('operator.*')">
                        {{ __('Dashboard Operator') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('ticket-check.index')" :active="request()->routeIs('ticket-check.*')">
                        {{ __('Verify Ticket') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->isSuperAdmin())
                    <x-responsive-nav-link :href="url('/super-admin')" :active="request()->routeIs('super-admin.*')">
                        {{ __('Dashboard Admin') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        @auth
            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    @if(Auth::user()->isOperator())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                            Operator
                        </span>
                    @elseif(Auth::user()->isSuperAdmin())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                            Super Admin
                        </span>
                    @endif
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('operator.register')">
                        {{ __('Register PO') }}
                    </x-responsive-nav-link>
                    <!-- Language Switcher Mobile -->
                    <div class="px-4 py-2 border-t mt-2">
                        <p class="text-xs text-gray-500 mb-2">Language</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('language.switch', 'id') }}" class="px-3 py-1 text-sm rounded {{ app()->getLocale() === 'id' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">🇮🇩 ID</a>
                            <a href="{{ route('language.switch', 'en') }}" class="px-3 py-1 text-sm rounded {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">🇬🇧 EN</a>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</nav>
