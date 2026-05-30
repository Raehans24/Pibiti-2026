@extends('layouts.app')
@section('content')
<h1 class="justify-center">Ini Adalah Halaman Dashboard</h1>
<div class="justify-center">
    <h2>Selamat datang di Dashboard User {{session('username')}}</h2>
</div>
<div class="justify-center">
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <button type="submit">Logout</button>
    </form>
    <button><a href="{{ route('notes') }}">Notes</a></button>
</div>
@endsection