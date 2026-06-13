@extends('layouts.app')
@section('content')
<div class="space-y-4">
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    <h2 class="text-3xl font-bold dark:text-white">
        Notes
    </h2>
    <p class="text-slate-500 mt-2 dark:text-white">
        Buat catatanmu lebih berwarna
    </p>
    <x-card>
        <h3 class="text-lg font-bold mb-6 dark:text-white">Tambahkan catatan</h3>
        <form action="{{ route('notes.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="mb-4">
                    <label class="block mb-2 text-slate-700 dark:text-white" for="title">Judul</label>
                    <input type="text" name="title" id="title" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white" required>
                </div>  
                <div class="mb-4">
                    <label class="block mb-2 text-slate-700 dark:text-white" for="content">Catatan</label>
                    <textarea rows="6" name="content" id="content" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white" required></textarea>
                </div>  
            </div>
            <div class="flex justify-end mt-4">
                <x-button type="submit" class="font-bold w-full">Simpan</x-button>
            </div>
        </form>
    </x-card>
    <x-card>
        <h3 class="text-lg font-semibold mb-6 dark:text-white">
            Upload Catatan
        </h3>
        <form action="{{ route('notes.upload') }}" method="post" enctype="multipart/form-data">
            @csrf   
            <input type="file" name="file" id="file" accept=".txt,.md,.pdf" class="flex w-full h-[100px] justify-center rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white cursor-pointer">
            <p class="text-xs text-slate-500 mt-1 dark:text-slate-400">Upload file .txt, .md, atau .pdf
            </p>
            @error('file')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <div class="flex justify-end mt-4">
                <x-button type="submit" class="font-bold w-full">Kirim</x-button>
            </div>
        </form>
    </x-card>
    <div class="">
        <h3 class="text-xl font-bold dark:text-white py-6">Catatan Saya</h3>
        <div class="space-y-4">
            @forelse ($notes as $note)
            <x-card>
                <h4 class="text-lg font-semibold dark:text-white">
                    {{ $note->title }}
                </h4>
                <p class="text-slate-500 mt-1 dark:text-slate-300">
                    {{ $note->content }}
                </p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <form action="{{ route('notes.summary', $note->id) }}" method="POST" class="inline">
                        @csrf
                        <x-button type="submit">
                            Simpulkan menggunakan Ai
                        </x-button>
                    </form>
                    <a href="{{ route('notes.show', $note->id) }}">
                        <x-button class="bg-emerald-500 hover:bg-emerald-600">
                            Detail
                        </x-button>
                    </a>
                    <form action="{{ route('notes.destroy', $note->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" class="bg-red-500 hover:bg-red-600">
                            Hapus
                        </x-button>
                    </form>
                </div>
            </x-card>
            @empty
            <x-card>
                <p class="text-slate-500 dark:text-slate-400 py-6">
                    Belum ada catatan yang dibuat
                </p>
            </x-card>
            @endforelse
        </div>
    </div>
</div>
@endsection
