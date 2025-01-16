<?php

namespace App\Jawaban;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NomorSatu {

    public function Auth(Request $request) { 
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $authData = [
            $loginField => $credentials['login'],
            'password' => $credentials['password']
        ];

        if (Auth::attempt($authData)) {
            $request->session()->regenerate();
            return redirect()->route('event.home')->with('message', ['Selamat datang kembali!', 'success']);
        }

        return redirect()->route('event.home')->with('message', ['Login gagal, silakan coba lagi.', 'danger']);
    }

    public function Logout(Request $request) { 
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('event.home')->with('message', ['Anda telah berhasil logout.', 'success']);
    }
}