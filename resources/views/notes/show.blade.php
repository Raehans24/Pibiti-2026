@extends('layouts.app')

@section('content')

{{-- Back navigation --}}
<div class="mb-5">
    <a href="{{ route('notes') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Catatan
    </a>
</div>

<div class="space-y-5">

    {{-- Main Note Card --}}
    <x-card>
        <form id="edit-note-form" action="{{ route('notes.update', $note->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Title Display --}}
            <div id="title-display-container" class="group cursor-pointer flex items-center justify-between mb-1">
                <h1 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $note->title }}
                </h1>
                <span class="text-xs text-slate-400 dark:text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Edit judul
                </span>
            </div>

            {{-- Title Input (hidden) --}}
            <div id="title-input-container" class="hidden mb-1">
                <input
                    type="text"
                    name="title"
                    id="title-input"
                    class="w-full text-xl font-bold text-slate-900 dark:text-white bg-transparent border-b-2 border-indigo-500 focus:outline-none pb-1"
                    value="{{ $note->title }}"
                >
            </div>

            <div class="h-px bg-slate-100 dark:bg-slate-700 my-4"></div>

            {{-- Content Display --}}
            <div id="content-display-container">
                @if (!empty($note->file_path))
                    @php $extension = pathinfo($note->file_path, PATHINFO_EXTENSION); @endphp
                    @if (strtolower($extension) === 'pdf')
                        <div class="w-full h-[500px] border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden mb-4">
                            <iframe src="{{ route('notes.file', $note->id) }}" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                    @endif
                @endif

                <div id="content-text-clicker"
                     class="group cursor-pointer p-4 rounded-xl border-2 border-dashed border-transparent hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/5 transition-all duration-200 min-h-[100px]">
                    @if (!empty($note->content))
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap text-sm">{{ $note->content }}</p>
                    @elseif (!empty($note->file_path))
                        <p class="text-slate-400 dark:text-slate-500 italic text-sm">Klik untuk menambahkan catatan teks tambahan...</p>
                    @else
                        <p class="text-slate-400 dark:text-slate-500 italic text-sm">Klik untuk menulis isi catatan...</p>
                    @endif
                    <span class="text-xs text-indigo-500 dark:text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity mt-2 inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        Klik untuk edit
                    </span>
                </div>
            </div>

            {{-- Content Input (hidden) --}}
            <div id="content-input-container" class="hidden mt-4">
                @if (!empty($note->file_path) && strtolower(pathinfo($note->file_path, PATHINFO_EXTENSION)) === 'pdf')
                    <div class="w-full h-[500px] border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden mb-4">
                        <iframe src="{{ route('notes.file', $note->id) }}" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                @endif
                <textarea
                    name="content"
                    id="content-textarea"
                    rows="10"
                    class="w-full p-4 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all resize-none"
                    placeholder="Tulis isi catatan di sini..."
                >{{ $note->content }}</textarea>
            </div>

            {{-- Edit Actions (hidden) --}}
            <div id="edit-actions" class="hidden justify-end gap-3 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" id="btn-cancel-edit"
                    class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 px-4 py-2 rounded-lg transition-colors cursor-pointer">
                    Batal
                </button>
                <x-button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-card>

    {{-- AI Summary --}}
    @if (!empty($note->summary))
    <x-card>
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-7 h-7 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-indigo-600 dark:text-indigo-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                </svg>
            </div>
            Ringkasan AI
        </h2>
        <div class="bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/30 rounded-xl p-4">
            <div class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap text-sm prose dark:prose-invert max-w-none">
                {!! $note->summary !!}
            </div>
        </div>
    </x-card>
    @endif

    {{-- AI Features --}}
    <x-card>
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <div class="w-7 h-7 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-emerald-600 dark:text-emerald-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
            Fitur AI
        </h2>
        <div class="flex flex-wrap gap-3">
            <form action="{{ route('notes.summary', $note->id) }}" method="POST" class="form-ai-action">
                @csrf
                <x-button type="submit" class="bg-indigo-600 hover:bg-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                    </svg>
                    Ringkas dengan AI
                </x-button>
            </form>

            <a href="/notes/{{ $note->id }}/quiz" class="btn-ai-action">
                <x-button class="bg-emerald-600 hover:bg-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                    Buat Quiz AI
                </x-button>
            </a>
        </div>
    </x-card>

</div>

@endsection