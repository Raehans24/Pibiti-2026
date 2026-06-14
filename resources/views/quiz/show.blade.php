@extends('layouts.app')
@section('content')

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('quiz') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Daftar Quiz
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                Quiz: {{ $note['title'] ?? 'Catatan' }}
            </h1>
        </div>
    </div>

    {{-- Quiz Result Banner --}}
    @if(session('quiz_result'))
        @php $result = session('quiz_result'); @endphp
        <div class="rounded-2xl p-5 border {{ $result['score'] >= 70
            ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700'
            : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700' }}">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0
                    {{ $result['score'] >= 70
                        ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400'
                        : 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400' }}">
                    @if($result['score'] >= 70)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    @endif
                </div>
                <div>
                    <h2 class="text-lg font-bold {{ $result['score'] >= 70
                        ? 'text-emerald-800 dark:text-emerald-300'
                        : 'text-red-800 dark:text-red-300' }}">
                        {{ $result['score'] >= 70 ? '🎉 Selamat! Kamu Lulus!' : '💪 Ayo Coba Lagi!' }}
                    </h2>
                    <p class="text-sm {{ $result['score'] >= 70
                        ? 'text-emerald-700 dark:text-emerald-400'
                        : 'text-red-700 dark:text-red-400' }}">
                        Skor: <strong class="text-2xl">{{ $result['score'] }}</strong>/100 —
                        Benar {{ $result['correct_count'] }} dari {{ $result['total_questions'] }} soal
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Questions --}}
    @php
        $quizData = $quiz ? $quiz->data : [];
        $questions = is_array($quizData) ? ($quizData['question'] ?? []) : ($quizData->question ?? []);
    @endphp

    @if(empty($questions))
        <x-card>
            <div class="flex flex-col items-center py-8 text-center">
                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm italic">Belum ada pertanyaan. Terjadi kesalahan pada AI saat men-generate kuis atau data kuis kosong.</p>
            </div>
        </x-card>
    @else
        <form action="{{ route('quiz.submit', request()->route('id')) }}" method="POST" class="space-y-4">
            @csrf

            @foreach($questions as $index => $q)
                @php
                    $qArray = is_array($q) ? $q : (array)$q;
                    $detail = session('quiz_result') ? session('quiz_result')['details'][$index] : null;
                @endphp

                <div class="bg-white dark:bg-slate-800 border rounded-2xl p-5 shadow-sm transition-shadow duration-200 {{
                    $detail
                        ? ($detail['is_correct']
                            ? 'border-emerald-300 dark:border-emerald-600 shadow-emerald-100 dark:shadow-emerald-900/20'
                            : 'border-red-300 dark:border-red-600 shadow-red-100 dark:shadow-red-900/20')
                        : 'border-slate-200 dark:border-slate-700 hover:shadow-md'
                }}">
                    {{-- Question Number & Text --}}
                    <div class="flex items-start gap-3 mb-4">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold
                            {{ $detail
                                ? ($detail['is_correct'] ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300')
                                : 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' }}">
                            {{ $index + 1 }}
                        </span>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white leading-relaxed">{{ $qArray['question'] ?? '' }}</p>
                    </div>

                    {{-- Options --}}
                    <div class="space-y-2">
                        @foreach(['A', 'B', 'C', 'D'] as $option)
                            @php
                                $optionKey = 'option_' . strtolower($option);
                                $isChecked = session('quiz_result') ? ($detail['user_answer'] === $option) : false;
                                $isCorrectAnswer = $detail && $detail['correct_answer'] === $option;
                                $isWrongAnswer = $detail && $detail['user_answer'] === $option && !$detail['is_correct'];
                            @endphp

                            <label class="quiz-option flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all duration-150
                                {{ $detail
                                    ? ($isCorrectAnswer
                                        ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-600'
                                        : ($isWrongAnswer
                                            ? 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-600'
                                            : 'bg-slate-50 dark:bg-slate-700/40 border-slate-200 dark:border-slate-600 opacity-50 cursor-not-allowed'))
                                    : 'border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/40 hover:border-indigo-300 dark:hover:border-indigo-600' }}">
                                <input
                                    type="radio"
                                    name="answers[{{ $index }}]"
                                    value="{{ $option }}"
                                    class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600 flex-shrink-0"
                                    {{ $isChecked ? 'checked' : '' }}
                                    {{ session('quiz_result') ? 'disabled' : 'required' }}
                                >
                                <span class="text-sm {{ $detail
                                    ? ($isCorrectAnswer
                                        ? 'text-emerald-800 dark:text-emerald-200 font-medium'
                                        : ($isWrongAnswer
                                            ? 'text-red-800 dark:text-red-200'
                                            : 'text-slate-500 dark:text-slate-400'))
                                    : 'text-slate-700 dark:text-slate-200' }}">
                                    <strong>{{ $option }}.</strong> {{ $qArray[$optionKey] ?? '' }}
                                </span>
                                @if($isCorrectAnswer)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 ml-auto flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                @endif
                                @if($isWrongAnswer)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-red-500 dark:text-red-400 ml-auto flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    {{-- Per-question result --}}
                    @if($detail)
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                            @if($detail['is_correct'])
                                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    Jawaban Benar!
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-600 dark:text-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
                                    </svg>
                                    Jawaban Salah — Jawaban benar: <strong>{{ $detail['correct_answer'] }}</strong>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Submit / Action Buttons --}}
            @if(!session('quiz_result'))
                <div class="flex justify-end pt-2">
                    <x-button type="submit" class="px-8">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        Kirim Jawaban
                    </x-button>
                </div>
            @else
                <div class="flex flex-wrap justify-between gap-3 pt-2">
                    <a href="{{ route('quiz') }}">
                        <x-button class="bg-slate-600 hover:bg-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Kembali ke Daftar
                        </x-button>
                    </a>
                    <a href="{{ route('quiz.generate', request()->route('id')) }}" class="btn-ai-action">
                        <x-button class="bg-emerald-600 hover:bg-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Generate Ulang Quiz
                        </x-button>
                    </a>
                </div>
            @endif
        </form>
    @endif

</div>
@endsection