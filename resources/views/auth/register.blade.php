@extends('layouts.auth')
@section('content')

<div class="min-h-screen flex items-center justify-center px-6 py-12 bg-slate-50 dark:bg-slate-900">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                </svg>
            </div>
            <span class="font-bold text-slate-900 dark:text-white text-xl">Smart Notes AI</span>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Buat akun baru</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Mulai perjalanan belajarmu bersama AI</p>

            @if ($errors->any())
                <div class="mb-5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl px-4 py-3">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-600 dark:text-red-400 text-sm flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="post" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="username">Username</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                        value="{{ old('username') }}"
                        placeholder="Pilih username kamu"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="email">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                        value="{{ old('email') }}"
                        placeholder="email@contoh.com"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                        placeholder="Minimal 8 karakter"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl py-3 text-sm transition-all duration-200 shadow-sm hover:shadow-md active:scale-[0.99] cursor-pointer mt-2"
                >
                    Buat Akun
                </button>
            </form>

            <p class="text-center text-slate-500 dark:text-slate-400 text-sm mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>

@endsection
