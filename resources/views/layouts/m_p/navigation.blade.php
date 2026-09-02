<style>
    .navbar-link {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        padding-left: 1rem;
        padding-right: 1rem;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 12px 0 rgba(220, 38, 38, 0.12);
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: linear-gradient(135deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%);
        font-size: 14px;
        border-radius: 0.75rem;
    }

    .navbar-link:hover {
        background: linear-gradient(90deg, #991b1b 0%, #7f1d1d 100%);
        color: #fff !important;
        box-shadow: 0 4px 16px 0 rgba(220, 38, 38, 0.15);
    }
</style>

<nav id="main-navbar" x-data="{ open: false }" class="sticky top-0 inset-x-0 z-[9990] w-full border-b border-gray-100 dark:border-gray-700 shadow-xl transition-all duration-300 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <!-- Logo (Left) -->
            <div class="shrink-0 flex items-center z-20">
                <a href="{{ route('welcome') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg group-hover:scale-105 transition-transform duration-300">
                        H
                    </div>
                    <span class="text-red-500 dark:text-white font-bold text-2xl tracking-tight group-hover:text-red-600 transition-colors duration-300">Kumwell</span>
                </a>
            </div>

            <!-- Navigation Links (Centered/Right) -->
            @php
                $user = auth()->user();
                $actionPendingCount = 0;

                if ($user) {
                    $userName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
                    $userFullName = trim($user->fullname ?? $userName);

                    $pendingQuery = \App\Models\ManpowerRequest::whereNotIn('status', ['approved', 'rejected', 'draft']);

                    if ($user->role !== 'admin') {
                        $pendingQuery->where(function($q) use ($user, $userName, $userFullName) {
                            $q->where(function($sub) use ($userName, $userFullName) {
                                $sub->where('status', 'pending_manager')
                                    ->where(function($m) use ($userName, $userFullName) {
                                        $m->where('manager_name', 'like', "%{$userName}%")
                                          ->orWhere('manager_name', 'like', "%{$userFullName}%")
                                          ->orWhereNull('manager_name')
                                          ->orWhere('manager_name', '');
                                    });
                            })
                            ->orWhere(function($sub) use ($userName, $userFullName) {
                                $sub->where('status', 'pending_vp')
                                    ->where(function($v) use ($userName, $userFullName) {
                                        $v->where('vp_name', 'like', "%{$userName}%")
                                          ->orWhere('vp_name', 'like', "%{$userFullName}%")
                                          ->orWhereNull('vp_name')
                                          ->orWhere('vp_name', '');
                                    });
                            });

                            if ($user->isHrOrAdmin()) {
                                $q->orWhere('status', 'pending_hr');
                            }
                            if ($user->level_user == '9') {
                                $q->orWhere('status', 'pending_ceo');
                            }
                        });
                    }

                    $actionPendingCount = $pendingQuery->count();
                }

                $mprPending = \App\Models\ManpowerRequest::where('user_id', auth()->id())
                    ->whereIn('status', ['pending_manager','pending_vp','pending_hr','pending_ceo'])
                    ->exists();
            @endphp

            <div class="hidden sm:flex sm:items-center gap-2 z-20">
                <a href="{{ route('welcome') }}" class="navbar-link shadow transition">
                    หน้าหลัก
                </a>

                <a href="{{ route('manpower-request.index') }}" class="navbar-link shadow transition flex items-center gap-1.5">
                    <span>ติดตามสถานะ</span>
                    @if($actionPendingCount > 0)
                        <span class="inline-flex items-center justify-center w-5 h-5 px-1 text-[11px] font-extrabold leading-none text-red-600 bg-white rounded-full shadow-sm">
                            {{ $actionPendingCount }}
                        </span>
                    @elseif($mprPending)
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                        </span>
                    @endif
                </a>

                @auth
                <div class="flex items-center gap-2 ms-3 sm:ms-4">
                    <!-- User Profile Dropdown Button -->
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="navbar-link p-1 text-white rounded-xl shadow flex items-center transition overflow-hidden cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center overflow-hidden border border-white/20">
                                        @if(Auth::user()->photo_user)
                                            <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            <i class="fa-solid fa-user text-white text-xs"></i>
                                        @endif
                                    </div>
                                    <svg class="fill-current h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <div class="font-bold text-sm text-gray-800 dark:text-gray-200">
                                    {{ Auth::user()->firstname ?? Auth::user()->name }} {{ Auth::user()->lastname ?? '' }}
                                </div>
                                <div class="text-xs text-gray-500 truncate mt-0.5">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>

                            @if(Route::has('profile.edit'))
                                <x-dropdown-link :href="route('profile.edit')">
                                    <i class="fa-solid fa-user-gear mr-2 text-xs"></i> {{ __('Profile') }}
                                </x-dropdown-link>
                            @elseif(Route::has('users.profile'))
                                <x-dropdown-link :href="route('users.profile', ['id' => auth()->id()])">
                                    <i class="fa-solid fa-user-gear mr-2 text-xs"></i> {{ __('Profile') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fa-solid fa-right-from-bracket mr-2 text-xs text-red-500"></i> {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    @include('layouts.partials.notification-bell')
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden gap-2">
                @include('layouts.partials.notification-bell')
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden absolute w-full bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-lg z-40">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                หน้าหลัก
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('manpower-request.index')" :active="request()->routeIs('manpower-request.*') || request()->routeIs('probation-evaluation.*') || request()->routeIs('interview-evaluation.*')">
                <div class="flex justify-between items-center w-full">
                    <span>ติดตามสถานะ</span>
                    @if($actionPendingCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-sm animate-pulse">
                            {{ $actionPendingCount }}
                        </span>
                    @elseif($mprPending)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">!</span>
                    @endif
                </div>
            </x-responsive-nav-link>
        </div>

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->firstname ?? Auth::user()->name }} {{ Auth::user()->lastname ?? '' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if(Route::has('profile.edit'))
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @elseif(Route::has('users.profile'))
                    <x-responsive-nav-link :href="route('users.profile', ['id' => auth()->id()])">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

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
        @endauth
    </div>
</nav>
