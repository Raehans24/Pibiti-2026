@extends('layouts.app')
@section('content')
<div class="space-y">
<h2 class="text-2xl font-bold text-red-400">
    Welcome Back {{ session('username') }}!
</h2>

<p class="text-slae-500 mb-6 dark:text-white">
    Ready to Learning Today?
</p>
<div class="grid md:grid-cols-3 gap-6">
<x-card>
    <p>
        Total Catatan
    </p>
    <h3 class="flex text-3xl font-bold mt-2">
        12
    </h3>
</x-card>
<x-card>
    <p>
        Total Catatan
    </p>
    <h3 class="text-3xl font-bold mt-2">
        12
    </h3>
</x-card>
<x-card>
    <p>
        Total Catatan
    </p>
    <h3 class="text-3xl font-bold mt-2">
        12
    </h3>
</x-card>
<x-card>
    <h3 class="font-bold mb-4">Aktifkan Terbaru</h3>

    <ul class="space-y-3">
        <li>
            Membuat Quiz
        </li>
        <li>
            Mengupload Image
        </li>
        <li>
            Membuat Ringkasan
        </li>
    </ul>
</x-card>
</div>
<div class="space-y">
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <x-button type="submit">
            Logout
        </x-button>
    </form>
</div>

@endsection