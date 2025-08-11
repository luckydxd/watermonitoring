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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'address' => 'required|string|max:500',
            'phone_number' => 'required|string|max:20|regex:/^[0-9]+$/',
        ], [
            'password.regex' => 'Kata sandi harus mengandung setidaknya satu huruf besar, satu huruf kecil, dan satu angka.',
            'phone_number.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi yang dimasukkan.',
        ]);

        $user = User::create([
            'id' => Str::uuid(),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        UserData::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        $userRole = Role::where('name', 'user')->first();
        $user->assignRole($userRole);

        auth()->login($user);

        return redirect()->route('user.dashboard')->with('success', 'Pendaftaran berhasil!');
    }
}
