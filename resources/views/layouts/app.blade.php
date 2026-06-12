<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name','Laravel') }}</title>
        {{-- Inline script: mencegah flash putih saat reload dengan menerapkan tema dari localStorage sebelum render --}}
        <script>
            (function() {
                const theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
        @if (file_exists(public_path('build/manifest.json'))||file_exists(public_path('build/assets')))
            @vite(['resources/css/app.css','resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @endif
        @stack('styles')
        @stack('scripts')
    </head>
    <body class="bg-gray-100 dark:bg-gray-900 text-slate-900 dark:text-slate-100 transition-colors duration-300">
        <div class="flex min-h-screen">
            <aside class="hidden md:block w-64 bg-slate-500 dark:bg-gray-800 text-white p-6 transition-colors duration-300" id="aside">
                <div class="text-xl font-bold mb-6">
                    <h1>Smart Notes Ai</h1>
                </div>
                <nav>
                    <a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded-lg hover:bg-slate-400 dark:hover:bg-gray-700 transition-colors duration-200">Dashboard</a>
                    <a href="{{ route('notes') }}" class="block py-2 px-3 rounded-lg hover:bg-slate-400 dark:hover:bg-gray-700 transition-colors duration-200">Notes</a>
                    <a href="{{ route('quiz') }}" class="block py-2 px-3 rounded-lg hover:bg-slate-400 dark:hover:bg-gray-700 transition-colors duration-200">Quiz</a>
                    <a href="{{ route('world') }}" class="block py-2 px-3 rounded-lg hover:bg-slate-400 dark:hover:bg-gray-700 transition-colors duration-200">🌍 World Prediction</a>
                </nav>
            </aside>
            <!-- Flex = 1 bukan L/l -->
            <main class="flex-1">  
                <header class="bg-slate-300 dark:bg-gray-800 border-b dark:border-gray-700 px-6 py-4 flex items-center justify-between transition-colors duration-300">
                    <div class="flex gap-4 items-center">
                        <button class="bg-blue-500 hover:bg-blue-300 rounded-lg
                        md:hidden
                        transition-colors duration-200"
                        id="menuButton">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h2 class="font-semibold dark:text-white"> Smart Notes Ai</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        {{-- Tombol Toggle Dark Mode --}}
                        <button id="darkModeToggle"
                            class="p-2 rounded-lg bg-gray-200 dark:bg-gray-400
                             hover:bg-gray-300 dark:hover:bg-white 
                             transition-colors duration-200 cursor-pointer"
                            title="Toggle Dark Mode">
                            {{-- Ikon Bulan (tampil saat Light Mode) --}}
                            <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                            </svg>
                            {{-- Ikon Matahari (tampil saat Dark Mode) --}}
                            <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            </svg>
                        </button>
                        <span class="dark:text-gray-300">Hello, {{ auth()->user()?->name }}</span>
                    </div>
                </header>
                <div class="p-5">
                    @yield('content')
                </div>
            </main>
        </div>

        @include('loading.Loading')
        @include('loading.Loading-ai')
    </body>
</html>