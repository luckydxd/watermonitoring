<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            $user = Auth::user();

            $token = auth('api')->login($user);
            session(['api_token' => $token]);

            return redirect()->intended($this->redirectPath());
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    protected function redirectPath()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return '/admin/dashboard';
        } elseif ($user->hasRole('teknisi')) {
            return '/teknisi/dashboard';
        }
        return '/user/dashboard';
    }
    public function logout(Request $request)
    {
        $user = $request->user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user && $user->hasRole('user')) {
            return redirect('/user/login');
        } else {
            return redirect('/admin/login');
        }
    }
}
