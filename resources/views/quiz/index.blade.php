@extends('layouts.app')
@section('content')
<div class="space-y">
<h2 class="text-2xl font-bold text-red-400">
    Welcome Back {{ session('username') }}!
</h2>

<p class="text-slate-500 mb-6 dark:text-slate-350">
    Ready to Learning Today?
</p>
<div class="space-y-4">
    @forelse($notes as $note)
    <x-card>
        <h3 class="font-bold">
            {{ $note['title'] }}
        </h3>
        <p class="text-slate-500 mt-2">
            {{ count($note['quizzes']) }} Quiz Terbuat
        </p>
        <div class="flex gap-2 mt-4">
            <a href="{{ route('quiz.show', $note['id']) }}">
                <x-button>
                    Masuk ke Quiz
                </x-button>
            </a>
            <form action="{{ route('quiz.destroy', $note['id']) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus kuis untuk catatan ini?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" class="bg-red-500 hover:bg-red-600">
                    Hapus
                </x-button>
            </form>
        </div>
    </x-card>
    @empty
    <div class="text-slate-500 italic">
        Belum ada quiz yang dibuat. Silakan klik tombol "Quiz AI" dari halaman Notes untuk membuat kuis.
    </div>
    @endforelse
</div>
</div>
@endsection