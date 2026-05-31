@extends('layouts.app')
@section('content')
<div class="space-y">
<h2 class="text-2xl font-bold text-red-400">
    Welcome Back {{ session('username') }}!
</h2>

<p class="text-slae-500 mb-6 dark:text-white">
    Ready to Learning Today?
</p>
<div class="space-y-4">
    <x-card>
        <h3 class="font-bold">
            Belajar Laravel
        </h3>
        <p class="text-slate-500 mt-2">
            5 Soal
        </p>
        <div class="flex gap-2 mt-4">
            <a href="{{ route('showQuiz') }}">
                <x-button>
                    Masuk ke Quiz
                </x-button>
            </a>
            <x-button class="bg-red-500">
                Hapus
            </x-button>
        </div>

        <h3 class="font-bold">
            Belajar Node.JS
        </h3>
        <p class="text-slate-500 mt-2">
            5 Soal
        </p>
        <div class="flex gap-2 mt-4">
            <a href="{{ route('showQuiz') }}">
                <x-button>
                    Masuk ke Quiz
                </x-button> 
            </a>
            <x-button class="bg-red-500">
                Hapus
            </x-button>
        </div>
    </x-card>
</div>
</div>
@endsection