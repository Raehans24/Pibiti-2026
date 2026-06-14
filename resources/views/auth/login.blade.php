@extends('layouts.auth')
@section('content')

<div class="min-h-screen flex">
    {{-- Left Panel – Branding --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 relative overflow-hidden flex-col justify-between p-12">
        {{-- Decorative circles --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                    </svg>
                </div>
                <span class="text-white font-bold text-xl">Smart Notes AI</span>
            </div>
        </div>

        <div class="relative z-10">
            <h1 class="text-4xl font-bold text-white leading-snug mb-4">
                Belajar lebih cerdas<br>dengan AI
            </h1>
            <p class="text-indigo-200 text-lg leading-relaxed">
                Buat catatan, rangkum otomatis, dan uji pemahamanmu dengan kuis yang dibuat AI.
            </p>

            <div class="mt-10 space-y-4">
                @foreach([
                    ['icon' => '📝', 'text' => 'Catatan pintar dengan dukungan AI'],
                    ['icon' => '🤖', 'text' => 'Ringkasan otomatis dari catatanmu'],
                    ['icon' => '🧠', 'text' => 'Kuis interaktif untuk menguji pemahamanmu'],
                ] as $feature)
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ $feature['icon'] }}</span>
                    <span class="text-white/80 text-sm">{{ $feature['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <p class="relative z-10 text-indigo-300 text-xs">© {{ date('Y') }} Smart Notes AI</p>
    </div>

    {{-- Right Panel – Form --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-white dark:bg-slate-900">
        <div class="w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900 dark:text-white">Smart Notes AI</span>
            </div>

            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Selamat datang kembali 👋</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-8">Masuk ke akun Smart Notes AI kamu</p>

            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-400 rounded-xl px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0 mt-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="post" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="username">Username</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <button
                    type="submit"
                    id="login-submit-btn"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl py-3 text-sm transition-all duration-200 shadow-sm hover:shadow-md active:scale-[0.99] cursor-pointer"
                >
                    Masuk
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                <span class="text-slate-400 dark:text-slate-500 text-xs font-medium uppercase tracking-wider">atau</span>
                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
            </div>

            {{-- Google Login --}}
            <a
                href="{{ route('auth.google') }}"
                id="google-login-btn"
                class="flex items-center justify-center gap-3 w-full bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-medium rounded-xl py-3 text-sm transition-all duration-200 border border-slate-300 dark:border-slate-600 shadow-sm hover:shadow-md"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Masuk dengan Google
            </a>

            <p class="text-center text-slate-500 dark:text-slate-400 text-sm mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                    Daftar sekarang
                </a>
            </p>
        </div>
    </div>
</div>

@endsection