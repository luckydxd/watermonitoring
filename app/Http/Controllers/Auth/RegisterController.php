<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Di dalam method register()
        $request->validate([
            'username' => 'required|string|max:255|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'phone_number.regex' => 'Phone number can only contain numbers.',
        ]);

        // Create user
        $user = User::create([
            'id' => Str::uuid(),
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create user data
        UserData::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        // Assign role
        $userRole = Role::where('name', 'user')->first();
        $user->assignRole($userRole);

        // Auto login after registration (optional)
        auth()->login($user);

        return redirect()->route('user.dashboard')->with('success', 'Registration successful!');
    }
}
