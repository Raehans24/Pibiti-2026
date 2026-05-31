@extends('layouts.app')
@section('content')
<div class="space-y-4">
    <h2 class="font-bold text-2xl">
        Pertanyaan
    </h2>
    <x-card>
        <h3 class="font-semibold mb-4">Soal 1</h3>
        <p class="mb-4"> 1. Apa itu Laravel?</p>
    </x-card>
    
    <x-card>
        <h3 class="font-semibold mb-4">Soal 2</h3>
        <p class="mb-4 font-bold"> 2. Apa itu composer?</p>
        <p class="mb-4"> A. Package Manager untuk PHP</p>
        <p class="mb-4"> B. Framework PHP</p>
        <p class="mb-4"> C. Testing Framework</p>
        <p class="mb-4"> D. Database Migrations</p>
    </x-card>
</div>
@endsection