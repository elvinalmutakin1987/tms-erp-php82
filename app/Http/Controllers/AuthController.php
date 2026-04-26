<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

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

    /**
     * Membuat request token
     */
    public function gen_request_token(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => (string) Str::uuid()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
