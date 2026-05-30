@extends('layouts.app')
@section('content')
<form action="/login" method="post">
    @csrf

    <label for="username">Username</label>
    <input type="text" name="username"> 
    <br>
    <label for="password">Password</label>
    <input type="password" name="password">
    <br>
    <button type="submit">Login</button>
    
</form>
@endsection