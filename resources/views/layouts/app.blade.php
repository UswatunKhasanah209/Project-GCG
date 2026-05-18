<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        :root {
            --soft-1: #edf2fb;
            --soft-2: #d7e3fc;
            --soft-3: #ccdbfd;
            --soft-4: #c1d3fe;
            --soft-5: #abc4ff;
            --text-main: #24324a;
            --text-muted: #5c6f96;
        }

        body {
            background:
                radial-gradient(circle at 85% 10%, rgba(171, 196, 255, .38), transparent 28rem),
                radial-gradient(circle at 10% 90%, rgba(193, 211, 254, .45), transparent 28rem),
                linear-gradient(135deg, #edf2fb 0%, #d7e3fc 48%, #abc4ff 100%);
            min-height: 100vh;
        }

        .glass {
            background: rgba(255, 255, 255, .62);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, .75);
        }

        .sidebar-open { width: 20rem; }
        .sidebar-closed { width: 5.5rem; }

        .main-open { margin-left: 20rem; }
        .main-closed { margin-left: 5.5rem; }

        .nav-open {
            left: 20rem;
            width: calc(100% - 20rem);
        }

        .nav-closed {
            left: 5.5rem;
            width: calc(100% - 5.5rem);
        }

        .sidebar-link {
            min-height: 4.25rem;
        }

        .sidebar-closed .sidebar-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-closed .sidebar-text,
        .sidebar-closed .sidebar-footer-text {
            display: none;
        }

        .sidebar-closed .sidebar-nav {
            padding-left: .85rem;
            padding-right: .85rem;
        }

        .sidebar-closed .toggle-btn {
            margin-left: auto;
            margin-right: auto;
        }

        .content-card {
            background:
                radial-gradient(circle at 92% 8%, rgba(186, 245, 255, .35), transparent 25rem),
                rgba(255, 255, 255, .45);
            border: 1px solid rgba(255, 255, 255, .78);
            backdrop-filter: blur(22px);
            box-shadow: 0 22px 60px rgba(92, 111, 150, .13);
        }

        .soft-footer {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 90% 0%, rgba(171, 196, 255, .55), transparent 18rem),
                linear-gradient(135deg, rgba(237, 242, 251, .92), rgba(215, 227, 252, .92), rgba(171, 196, 255, .88));
            border: 1px solid rgba(255, 255, 255, .75);
            backdrop-filter: blur(20px);
            box-shadow: 0 18px 45px rgba(92, 111, 150, .16);
        }

        .soft-footer::before {
            content: "";
            position: absolute;
            width: 18rem;
            height: 18rem;
            right: -6rem;
            top: -9rem;
            background: rgba(255, 255, 255, .38);
            filter: blur(50px);
            border-radius: 999px;
        }

        @media (max-width: 1024px) {
            .sidebar-open,
            .sidebar-closed {
                width: 5.5rem;
            }

            .main-open,
            .main-closed {
                margin-left: 5.5rem;
            }

            .nav-open,
            .nav-closed {
                left: 5.5rem;
                width: calc(100% - 5.5rem);
            }

            .sidebar-text,
            .sidebar-footer-text {
                display: none;
            }
        }
    </style>
</head>

<body class="font-sans antialiased text-[#24324a]">

<div class="min-h-screen">

    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="sidebar-open fixed left-0 top-0 h-screen z-50
        bg-gradient-to-b from-[#edf2fb] via-[#ccdbfd] to-[#abc4ff]
        border-r border-white/70 shadow-2xl shadow-[#abc4ff]/25
        transition-all duration-300 overflow-hidden">

        <div class="h-full flex flex-col">

            {{-- TOGGLE --}}
            <div class="px-5 pt-6">
                <button onclick="toggleSidebar()"
                    class="toggle-btn w-12 h-12 rounded-2xl bg-white/85 hover:bg-white
                    text-[#4c638f] shadow-md flex items-center justify-center text-xl transition">
                    ☰
                </button>
            </div>

            {{-- MENU --}}
            <nav class="sidebar-nav flex-1 px-5 py-10">
                <div class="space-y-5">

                    <a href="{{ route('dashboard') }}"
                        class="sidebar-link flex items-center gap-5 px-5 rounded-[1.7rem] transition
                        {{ request()->routeIs('dashboard') ? 'bg-white text-[#24324a] shadow-lg' : 'bg-white/42 text-[#4c638f] hover:bg-white/80 hover:shadow-lg' }}">
                        <span class="text-2xl shrink-0">🏠</span>
                        <span class="sidebar-text font-bold text-lg">Dashboard</span>
                    </a>

                    <a href="{{ route('aspek.gcg.index') }}"
                        class="sidebar-link flex items-center gap-5 px-5 rounded-[1.7rem] transition
                        {{ request()->routeIs('aspek.gcg.*') ? 'bg-white text-[#24324a] shadow-lg' : 'bg-white/42 text-[#4c638f] hover:bg-white/80 hover:shadow-lg' }}">
                        <span class="text-2xl shrink-0">📒</span>
                        <span class="sidebar-text font-bold text-lg">Aspek GCG</span>
                    </a>

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('penilaian.index') }}"
                            class="sidebar-link flex items-center gap-5 px-5 rounded-[1.7rem] transition
                            {{ request()->routeIs('penilaian.*') ? 'bg-white text-[#24324a] shadow-lg' : 'bg-white/42 text-[#4c638f] hover:bg-white/80 hover:shadow-lg' }}">
                            <span class="text-2xl shrink-0">📝</span>
                            <span class="sidebar-text font-bold text-lg">Penilaian</span>
                        </a>
                    @endif

                    <a href="{{ route('library.index') }}"
                        class="sidebar-link flex items-center gap-5 px-5 rounded-[1.7rem] transition
                        {{ request()->routeIs('library.*') ? 'bg-white text-[#24324a] shadow-lg' : 'bg-white/42 text-[#4c638f] hover:bg-white/80 hover:shadow-lg' }}">
                        <span class="text-2xl shrink-0">📚</span>
                        <span class="sidebar-text font-bold text-lg">Library</span>
                    </a>

                    <a href="{{ route('profile.index') }}"
                        class="sidebar-link flex items-center gap-5 px-5 rounded-[1.7rem] transition
                        {{ request()->routeIs('profile.*') ? 'bg-white text-[#24324a] shadow-lg' : 'bg-white/42 text-[#4c638f] hover:bg-white/80 hover:shadow-lg' }}">
                        <span class="text-2xl shrink-0">👤</span>
                        <span class="sidebar-text font-bold text-lg">Profil</span>
                    </a>

                </div>
            </nav>

            <div class="px-5 pb-6">
                <div class="glass rounded-[1.7rem] p-4 text-[#5c6f96]">
                    <p class="sidebar-footer-text text-sm leading-relaxed">
                        © INKA 2026<br>
                        PT Industri Kereta Api
                    </p>
                </div>
            </div>

        </div>
    </aside>

    {{-- NAVBAR --}}
    <nav id="navbar"
        class="nav-open fixed top-0 right-0 h-24 z-40
        bg-white/48 backdrop-blur-xl border-b border-white/70
        shadow-lg shadow-[#abc4ff]/10 transition-all duration-300">

        <div class="h-full px-8 flex items-center justify-between">

            <div class="flex items-center gap-4">
                <div class="glass rounded-3xl px-5 py-3 flex items-center gap-4 shadow-sm">
                    <img src="{{ asset('images/danantara.png') }}" class="h-10 object-contain" alt="Danantara">
                    <div class="w-px h-9 bg-[#abc4ff]"></div>
                    <img src="{{ asset('images/inka.png') }}" class="h-10 object-contain" alt="INKA">
                </div>

                <div class="hidden md:block">
                    <h1 class="text-lg font-black text-[#24324a]">
                        PT Industri Kereta Api (Persero)
                    </h1>
                </div>
            </div>

            <div class="relative">
                <button onclick="toggleProfile()"
                    class="glass rounded-3xl px-4 py-3 flex items-center gap-3 shadow-sm hover:shadow-md transition">

                    <div class="hidden sm:block text-right leading-tight">
                        <p class="text-sm font-black text-[#24324a]">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-[#6d7ea6]">
                            {{ auth()->user()->role === 'admin' ? 'Admin' : 'User' }}
                        </p>
                    </div>

                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                            class="w-11 h-11 rounded-2xl object-cover ring-4 ring-white"
                            alt="Avatar">
                    @else
                        <div class="w-11 h-11 rounded-2xl bg-[#abc4ff] text-white flex items-center justify-center font-black ring-4 ring-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <span class="text-[#5c6f96]">⌄</span>
                </button>

                <div id="profileMenu"
                    class="hidden absolute right-0 mt-4 w-52 bg-white rounded-2xl shadow-xl border border-[#d7e3fc] overflow-hidden z-50">

                    <a href="{{ route('profile.index') }}"
                        class="block px-5 py-3 text-[#24324a] hover:bg-[#edf2fb] transition">
                        Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-5 py-3 text-red-500 hover:bg-red-50 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </nav>

    {{-- MAIN --}}
    <div id="mainContent"
        class="main-open min-h-screen transition-all duration-300 flex flex-col">

        <main class="pt-32 px-8 pb-8 flex-1">
            <div class="content-card rounded-[2rem] min-h-[calc(100vh-12rem)] p-6 md:p-8">
                @yield('content')
            </div>
        </main>

        {{-- FOOTBAR --}}
        <footer class="soft-footer mx-8 mb-8 rounded-[2rem] text-[#24324a]">

            <div class="relative z-10 px-8 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">

                <div>
                    <p class="text-[#24324a] text-sm font-black tracking-wide mb-4">
                        PT INDUSTRI KERETA API (PERSERO)
                    </p>

                    <img src="{{ asset('images/inka.png') }}"
                        class="h-12 mb-4 opacity-80"
                        alt="INKA">

                    <p class="text-sm leading-relaxed text-[#5c6f96]">
                        Sistem Penilaian Good Corporate Governance berbasis digital untuk monitoring,
                        evaluasi, dan dokumentasi penilaian perusahaan.
                    </p>
                </div>

                <div class="text-sm leading-relaxed space-y-2 text-[#5c6f96]">
                    <p class="font-bold text-[#24324a]">Kantor Pusat</p>
                    <p>Jl. Yos Sudarso No. 71 Madiun 63122, Jawa Timur</p>
                    <p>Telp. <b>(0351) 452271–74</b></p>
                    <p>Fax. <b>(0351) 452275</b></p>
                    <p>Email: <b>sekretariat@inka.co.id</b></p>
                </div>

                <div class="text-sm leading-relaxed space-y-2 text-[#5c6f96]">
                    <p class="font-bold text-[#24324a]">Bisnis & Perwakilan</p>
                    <p>Bisnis & Pemasaran:</p>
                    <p><b>pemasarankeretaapi@inka.co.id</b></p>
                    <p class="pt-2 font-bold text-[#24324a]">Kantor Jakarta</p>
                    <p>Jl. Tebet Barat VIII No. 3, Jakarta Selatan</p>
                    <p>Email: <b>inkajkt@inka.co.id</b></p>
                </div>

                <div class="text-sm leading-relaxed space-y-3 text-[#5c6f96]">
                    <p class="font-bold text-[#24324a]">Pabrik INKA Banyuwangi</p>
                    <p>
                        Jalan INKA Banyuwangi, Lingkar Kampung Baru, Kalipuro,
                        Kabupaten Banyuwangi, Jawa Timur, 68455.
                    </p>

                    <div class="rounded-2xl bg-white/45 border border-white/60 p-4 shadow-sm">
                        <p class="text-[#24324a] font-bold mb-1">GCG Assessment System</p>
                        <p class="text-xs text-[#5c6f96]">
                            Dashboard, Penilaian, Library Dokumen, dan Laporan GCG.
                        </p>
                    </div>
                </div>

            </div>

            <div class="relative z-10 px-8 py-4 border-t border-white/60 flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-xs tracking-wide text-[#5c6f96]">
                    © INKA 2026. ALL RIGHTS RESERVED.
                </p>

                <p class="text-xs text-[#5c6f96]">
                    PT Industri Kereta Api (Persero)
                </p>
            </div>

        </footer>

    </div>

</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const navbar = document.getElementById('navbar');
    const mainContent = document.getElementById('mainContent');
    let isOpen = true;

    function toggleSidebar() {
        if (isOpen) {
            sidebar.classList.remove('sidebar-open');
            sidebar.classList.add('sidebar-closed');

            navbar.classList.remove('nav-open');
            navbar.classList.add('nav-closed');

            mainContent.classList.remove('main-open');
            mainContent.classList.add('main-closed');
        } else {
            sidebar.classList.remove('sidebar-closed');
            sidebar.classList.add('sidebar-open');

            navbar.classList.remove('nav-closed');
            navbar.classList.add('nav-open');

            mainContent.classList.remove('main-closed');
            mainContent.classList.add('main-open');
        }

        isOpen = !isOpen;
    }

    function toggleProfile() {
        document.getElementById('profileMenu').classList.toggle('hidden');
    }

    document.addEventListener('click', function (e) {
        const menu = document.getElementById('profileMenu');

        if (!e.target.closest('#profileMenu') && !e.target.closest('[onclick="toggleProfile()"]')) {
            menu.classList.add('hidden');
        }
    });
</script>

@stack('scripts')

</body>
</html>