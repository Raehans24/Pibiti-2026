@extends('layouts.app')

@section('content')
<div class="space-y-9">
    <div class="">

        <a href="{{ route('notes') }}" class="text-indigo-600 dark:indigo-400 hover:underline">
            Kembali
        </a>
    </div>
</div>
<x-card>
    <form id="edit-note-form" action="{{ route('notes.update', $note['id']) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Title Display Section -->
        <div id="title-display-container" class="group cursor-pointer flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                {{ $note['title'] }}
            </h2>
            <span class="text-xs text-slate-400 dark:text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">Klik untuk mengedit judul</span>
        </div>

        <!-- Title Input Section (Hidden by default) -->
        <div id="title-input-container" class="hidden">
            <input type="text" name="title" id="title-input" class="w-full text-xl font-bold text-slate-900 dark:text-white bg-transparent border-b border-indigo-600 dark:border-indigo-400 focus:outline-none focus:border-b-2 pb-1" value="{{ $note['title'] }}">
        </div>

        <!-- Content Display Section -->
        <div id="content-display-container" class="mt-4">
            @if (!empty($note['file_path']))
                @php
                    $extension = pathinfo($note['file_path'], PATHINFO_EXTENSION);
                @endphp

                @if (strtolower($extension) === 'pdf')
                    <div class="w-full h-[600px] border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden mb-4">
                        <iframe src="{{ route('notes.file', $note['id']) }}" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                @endif
            @endif

            <div id="content-text-clicker" class="group cursor-pointer p-3 rounded-lg border border-dashed border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 min-h-[40px]">
                @if (!empty($note['content']))
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $note['content'] }}</p>
                @elseif (!empty($note['file_path']))
                    <p class="text-slate-400 dark:text-slate-500 italic">Klik di sini untuk menulis/menambahkan catatan tambahan di bawah preview file...</p>
                @else
                    <p class="text-slate-400 dark:text-slate-500 italic">Klik di sini untuk menulis isi catatan...</p>
                @endif
            </div>
        </div>

        <!-- Content Input Section (Hidden by default) -->
        <div id="content-input-container" class="hidden mt-4">
            @if (!empty($note['file_path']) && strtolower(pathinfo($note['file_path'], PATHINFO_EXTENSION)) === 'pdf')
                <div class="w-full h-[600px] border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden mb-4">
                    <iframe src="{{ route('notes.file', $note['id']) }}" class="w-full h-full" frameborder="0"></iframe>
                </div>
            @endif

            <textarea name="content" id="content-textarea" rows="8" class="w-full p-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Tulis isi catatan di sini...">{{ $note['content'] }}</textarea>
        </div>

        <!-- Edit Actions Buttons (Hidden by default) -->
        <div id="edit-actions" class="hidden justify-end gap-3 mt-4">
            <x-button type="button" id="btn-cancel-edit" class="bg-slate-500 dark:bg-slate-600 hover:bg-slate-600 dark:hover:bg-slate-700">
                Batal
            </x-button>
            <x-button type="submit">
                Simpan Perubahan
            </x-button>
        </div>
    </form>
</x-card>

@if (!empty($note['summary']))
<x-card>
    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
        Ringkasan AI
    </h3>
    <div class="text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
        {!! $note['summary'] !!}
    </div>
</x-card>
@endif

<x-card>
    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
        Fitur AI
    </h3>
    <div class="flex flex-wrap gap-4">
        <form action="{{ route('notes.summary', $note['id']) }}" method="POST" class="form-ai-action">
            @csrf
            <x-button type="submit">
                Ringkas AI
            </x-button>
        </form>
        <a href="/notes/{{ $note['id'] }}/quiz" class="btn-ai-action">
            <x-button class="bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700">  
                Quiz AI
            </x-button>
        </a>
    </div>
</x-card>


@endsection