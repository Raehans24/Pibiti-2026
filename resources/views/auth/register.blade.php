@extends('layouts.auth')
@section('content')

<!-- Container -->
<div class="min-h-screen flex bg-blue-500 items-center justify-center">

<div class="bg-white rounded-xl p-10 shadow-md max-w-md w-full">
    <h1 class="text-2xl font-bold mb-7 text-center text-slate-900">Smart Notes Ai - Register</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-500 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="post">
        @csrf

        <div class="mb-4">
            <label class="block mb-2 font-medium text-slate-900" for="username">Username</label>
            <input type="text" name="username" class="w-full rounded-lg border p-2 text-slate-900 bg-white" value="{{ old('username') }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-medium text-slate-900" for="email">Email</label>
            <input type="email" name="email" class="w-full rounded-lg border p-2 text-slate-900 bg-white" value="{{ old('email') }}" required>
        </div>
        <div class="mb-6">
            <label class="block mb-2 font-medium text-slate-900" for="password">Password</label>
            <input class="w-full rounded-lg border p-2 text-slate-900 bg-white" type="password" name="password" required>
        </div>
        <div class="mb-4">
            <x-button type="submit" class="w-full font-bold hover:bg-blue-600 text-white cursor-pointer transition-colors duration-200">Register</x-button>
        </div>

        <div class="text-center mt-4">
            <span class="text-gray-600">Sudah punya akun? </span>
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login di sini</a>
        </div>
    </form>
</div>
</div>
@endsection
