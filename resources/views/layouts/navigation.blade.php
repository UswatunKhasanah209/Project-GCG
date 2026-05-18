@php
    $navbarUser = Auth::user();

    $navbarAvatar = $navbarUser && $navbarUser->avatar
        ? asset('storage/' . $navbarUser->avatar) . '?v=' . optional($navbarUser->updated_at)->timestamp
        : null;
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('penilaian.index')" :active="request()->routeIs('penilaian.*')">
                        {{ __('Penilaian') }}
                    </x-nav-link>

                    <x-nav-link :href="route('profile.index')" :active="request()->routeIs('profile.*')">
                        {{ __('Profil') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-3 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-600 bg-white hover:text-gray-800 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">

                            @if($navbarAvatar)
                                <img
                                    src="{{ $navbarAvatar }}"
                                    alt="Foto Profil"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-[#2e7892] bg-white"
                                >
                            @else
                                <div class="w-10 h-10 rounded-full border-2 border-[#2e7892] flex items-center justify-center text-[#2e7892] bg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0115 0"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="max-w-[140px] truncate">
                                {{ $navbarUser->name }}
                            </div>

                            <svg class="fill-current h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.index')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.account')">
                            {{ __('Akun') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Edit Profil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('penilaian.index')" :active="request()->routeIs('penilaian.*')">
                {{ __('Penilaian') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('profile.index')" :active="request()->routeIs('profile.*')">
                {{ __('Profil') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center gap-3">
                @if($navbarAvatar)
                    <img
                        src="{{ $navbarAvatar }}"
                        alt="Foto Profil"
                        class="w-12 h-12 rounded-full object-cover border-2 border-[#2e7892] bg-white"
                    >
                @else
                    <div class="w-12 h-12 rounded-full border-2 border-[#2e7892] flex items-center justify-center text-[#2e7892] bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0115 0"/>
                        </svg>
                    </div>
                @endif

                <div class="min-w-0">
                    <div class="font-medium text-base text-gray-800 truncate">
                        {{ $navbarUser->name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500 truncate">
                        {{ $navbarUser->email }}
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.index')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.account')">
                    {{ __('Akun') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Edit Profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>