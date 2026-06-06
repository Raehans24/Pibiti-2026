@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="font-bold text-2xl text-slate-900 dark:text-white">
            Quiz: {{ $note['title'] ?? 'Notes' }}
        </h2>
        <a href="{{ route('quiz') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Kembali</a>
    </div>

    @if(session('quiz_result'))
        @php $result = session('quiz_result'); @endphp
        <div class="p-4 rounded-lg {{ $result['score'] >= 70 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
            <h3 class="font-bold text-lg mb-1">Hasil Kuis</h3>
            <p>Skor Anda: <strong>{{ $result['score'] }}</strong> (Benar {{ $result['correct_count'] }} dari {{ $result['total_questions'] }})</p>
        </div>
    @endif

    @php
        // Handle array or object structure depending on Laravel AI parsing
        $questions = is_array($quiz) ? ($quiz['question'] ?? []) : ($quiz->question ?? []);
    @endphp

    @if(empty($questions))
        <x-card>
            <p class="text-slate-500 italic">Belum ada pertanyaan. Terjadi kesalahan pada AI saat men-generate kuis atau data kuis kosong.</p>
        </x-card>
    @else
        <form action="{{ route('quiz.submit', request()->route('id')) }}" method="POST" class="space-y-6">
            @csrf
            
            @foreach($questions as $index => $q)
                @php
                    $qArray = is_array($q) ? $q : (array)$q;
                    $detail = session('quiz_result') ? session('quiz_result')['details'][$index] : null;
                @endphp
                <x-card class="{{ $detail ? ($detail['is_correct'] ? 'border-2 border-emerald-500' : 'border-2 border-red-500') : '' }}">
                    <h3 class="font-semibold mb-4 text-slate-900 dark:text-white">Soal {{ $index + 1 }}</h3>
                    <p class="mb-4 font-bold text-slate-800 dark:text-slate-200">{{ $qArray['question'] ?? '' }}</p>
                    
                    <div class="space-y-3">
                        @foreach(['A', 'B', 'C', 'D'] as $option)
                            @php
                                $optionKey = 'option_' . strtolower($option);
                                $isChecked = session('quiz_result') ? ($detail['user_answer'] === $option) : false;
                                
                                $labelClass = "block p-3 border rounded-lg cursor-pointer transition-colors dark:border-slate-700";
                                if (session('quiz_result')) {
                                    if ($detail['correct_answer'] === $option) {
                                        $labelClass .= " bg-emerald-100 border-emerald-500 dark:bg-emerald-900/40 dark:border-emerald-500";
                                    } elseif ($detail['user_answer'] === $option && !$detail['is_correct']) {
                                        $labelClass .= " bg-red-100 border-red-500 dark:bg-red-900/40 dark:border-red-500";
                                    } else {
                                        $labelClass .= " bg-slate-50 dark:bg-slate-800 opacity-50 cursor-not-allowed";
                                    }
                                } else {
                                    $labelClass .= " hover:bg-slate-50 dark:hover:bg-slate-800";
                                }
                            @endphp
                            <label class="{{ $labelClass }}">
                                <div class="flex items-center">
                                    <input type="radio" 
                                           name="answers[{{ $index }}]" 
                                           value="{{ $option }}" 
                                           class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600"
                                           {{ $isChecked ? 'checked' : '' }}
                                           {{ session('quiz_result') ? 'disabled' : 'required' }}>
                                    <span class="ml-3 text-slate-700 dark:text-slate-300">
                                        <strong>{{ $option }}.</strong> {{ $qArray[$optionKey] ?? '' }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    
                    @if($detail)
                        <div class="mt-4 pt-4 border-t dark:border-slate-700">
                            @if($detail['is_correct'])
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    Benar!
                                </span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-bold flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                    Salah. Jawaban yang benar adalah {{ $detail['correct_answer'] }}
                                </span>
                            @endif
                        </div>
                    @endif
                </x-card>
            @endforeach
            
            @if(!session('quiz_result'))
                <div class="flex justify-end pt-4">
                    <x-button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition-colors">
                        Kirim Jawaban
                    </x-button>
                </div>
            @else
                <div class="flex justify-between pt-4">
                    <a href="{{ route('quiz') }}">
                        <x-button type="button" class="bg-slate-500 hover:bg-slate-600">
                            Kembali ke Daftar Kuis
                        </x-button>
                    </a>
                    <a href="{{ route('quiz.generate', request()->route('id')) }}" class="btn-ai-action">
                        <x-button type="button" class="bg-emerald-600 hover:bg-emerald-700">
                            Generate Ulang Kuis
                        </x-button>
                    </a>
                </div>
            @endif
        </form>
    @endif
</div>
@endsection