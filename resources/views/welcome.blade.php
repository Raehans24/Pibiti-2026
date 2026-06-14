@extends('layouts.auth')
@section('content')

{{-- Minimalist Navbar --}}
<nav class="absolute top-0 w-full z-50">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- Logo --}}
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-slate-900 dark:text-white">
                    <path fill-rule="evenodd" d="M4.5 3.75a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V6.75a3 3 0 0 0-3-3h-15Zm4.125 3a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Zm-3.873 8.703a4.126 4.126 0 0 1 7.746 0 .75.75 0 0 1-.71.972H5.462a.75.75 0 0 1-.71-.972ZM15 8.25a.75.75 0 0 0 0 1.5h3.75a.75.75 0 0 0 0-1.5H15ZM14.25 12a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H15a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3.75a.75.75 0 0 0 0-1.5H15Z" clip-rule="evenodd" />
                </svg>
                <span class="font-semibold text-slate-900 dark:text-white tracking-tight">Smart Notes</span>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-6">
                <button id="darkModeToggle" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors" title="Toggle Dark Mode">
                    <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </button>

                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-900 dark:text-white hover:underline underline-offset-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 dark:bg-white px-4 py-2 text-sm font-medium text-white dark:text-slate-900 transition-colors hover:bg-slate-800 dark:hover:bg-slate-100">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Hero Section --}}
<div class="relative pt-32 pb-20 sm:pt-48 sm:pb-32 flex flex-col items-center justify-center min-h-[85vh]">
    <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
        
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-slate-900 dark:text-white tracking-tight leading-[1.1] mb-6">
            Cara paling cerdas <br class="hidden sm:block"> untuk belajar.
        </h1>

        <p class="mt-6 text-lg sm:text-xl text-slate-500 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
            Smart Notes menggunakan AI untuk merangkum materi Anda dan secara otomatis membuat kuis yang dipersonalisasi. Belajar lebih sedikit, pahami lebih banyak.
        </p>

        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('register') }}" class="inline-flex h-12 items-center justify-center rounded-lg bg-slate-900 dark:bg-white px-8 text-base font-medium text-white dark:text-slate-900 transition-colors hover:bg-slate-800 dark:hover:bg-slate-100">
                Mulai Sekarang
            </a>
            <a href="#fitur" class="inline-flex h-12 items-center justify-center rounded-lg bg-white dark:bg-slate-900 px-8 text-base font-medium text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</div>

{{-- Features Section --}}
<div id="fitur" class="py-24 sm:py-32 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800/50">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 sm:gap-8">
            
            {{-- Feature 1 --}}
            <div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-900 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Satu Tempat untuk Semua</h3>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed text-base">
                    Unggah catatan kelas Anda dalam format PDF, Markdown, atau TXT. Kami menyimpannya di ruang kerja yang rapi dan mudah diakses.
                </p>
            </div>

            {{-- Feature 2 --}}
            <div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-900 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Intisari Instan</h3>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed text-base">
                    Teknologi AI kami mengekstrak poin-poin paling penting dari materi Anda, menghemat berjam-jam waktu membaca.
                </p>
            </div>

            {{-- Feature 3 --}}
            <div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-900 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Latihan Otomatis</h3>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed text-base">
                    Uji diri Anda secara langsung. Dapatkan kuis pilihan ganda yang secara otomatis dibuat berdasarkan materi yang Anda pelajari.
                </p>
            </div>

        </div>
    </div>
</div>

{{-- Minimalist CTA Section --}}
<div class="py-24 sm:py-32">
    <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white mb-6">Mulai efisiensikan belajarmu.</h2>
        <p class="text-lg text-slate-500 dark:text-slate-400 mb-10 max-w-xl mx-auto">
            Bergabung gratis. Tidak memerlukan kartu kredit.
        </p>
        <a href="{{ route('register') }}" class="inline-flex h-12 items-center justify-center rounded-lg bg-slate-900 dark:bg-white px-8 text-base font-medium text-white dark:text-slate-900 transition-colors hover:bg-slate-800 dark:hover:bg-slate-100">
            Daftar Sekarang
        </a>
    </div>
</div>

{{-- Footer --}}
<footer class="border-t border-slate-100 dark:border-slate-800/50 py-12">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <span class="font-semibold text-slate-900 dark:text-white tracking-tight">Smart Notes</span>
        <p class="text-slate-400 dark:text-slate-500 text-sm">
            © {{ date('Y') }} Smart Notes. Hak cipta dilindungi.
        </p>
    </div>
</footer>

@endsection
