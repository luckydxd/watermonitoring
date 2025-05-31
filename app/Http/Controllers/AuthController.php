<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = $request->user();

            if ($user->hasRole('admin')) {
                return redirect()->intended('admin/dashboard');
            } elseif ($user->hasRole('teknisi')) {
                return redirect()->intended('teknisi/dashboard');
            } elseif ($user->hasRole('user')) {
                return redirect()->intended('user/dashboard');
            }


            return redirect()->intended('/');
        }


        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        // Get the user's role before logging out
        $user = $request->user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on role
        if ($user && $user->hasRole('user')) {
            return redirect('/user/login');
        } else {
            return redirect('/admin/login');
        }
    }
}
