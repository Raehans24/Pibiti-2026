@extends('layouts.auth')
@section('content')

<!-- Container -->
<div class="min-h-screen flex bg-blue-500
items-center justify-center ">

<div class="bg-white rounded-xl p-10 shadow-md max-w-md">
    <h1 class="text-2xl font-bold m-7 text-center">Smart Notes Ai</h1>
    <form action="/login" method="post">
        @csrf

        <div class="mb-4 ">
            <label class="mb-5" for="username">Username</label>
            <input type="text" name="username" class="w-full rounded-lg full border">
        </div>
        <div class="mb-4">
            <label class="mb-5" for="password">Password</label>
            <input class="w-full rounded-lg full border " type="text" name="password">
        </div>
        <div class="mb-4">
            <x-button type="submit"
            class="w-full font-bold
             hover:bg-blue-300 hover text-white
             cursor-pointer transition-colors duration-200
             ">Login</x-button>
        </div>

    </form>
</div>
</div>
@endsection