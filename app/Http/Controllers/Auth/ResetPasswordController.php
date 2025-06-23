<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password baru tidak boleh sama dengan password lama.'],
            ]);
        }

        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                Auth::login($user);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? redirect($this->redirectPath())->with('status', trans($response))
            : back()->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    protected function redirectPath()
    {
        $user = Auth::user();

        if ($user && $user->hasRole('admin')) {
            return '/admin/dashboard';
        } elseif ($user && $user->hasRole('teknisi')) {
            return '/teknisi/dashboard';
        } elseif ($user && $user->hasRole('user')) {
            return '/user/dashboard';
        }

        return '/user/login';
    }
}
