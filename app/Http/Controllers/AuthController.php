<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function check_login(Request $request)
    {
        if (!$request->username || !$request->password) {
            return redirect()->route('login')->with([
                'status' => 'failed',
                'message' => 'Username and password are required!'
            ]);
        }

        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255']
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended()->with([
                'status' => 'success',
                'message' => 'Welcome to ' . env('APP_NAME')
            ]);
        }

        return redirect()->route('login')->withErrors($credentials)->with([
            'status' => 'failed',
            'message' => 'Your username or password is incorrect!'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
