<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin (){
        return view('auth.login');
    }

    public function login(Request $request){
        $username = $request->username;
        $password = $request->password;

        if($username === "admin"
        && $password === "123"){
            session([
                'is_login' => true,
                'username' => $username
            ]);

            return redirect()->route('dashboard');
        }

        return redirect()->route('login');
    }
    
    public function logout(){
        session()->forget(['is_login','username']);
        return redirect()->route('login');
    }
}
