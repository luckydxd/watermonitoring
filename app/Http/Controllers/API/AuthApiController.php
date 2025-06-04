<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserData;
use App\Models\Device;

use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


class AuthApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'deviceLogin']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Only JSON requests are accepted'], 415);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|max:20'
        ]);

        $user = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        UserData::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        $userRole = Role::where('name', 'user')->first();
        $user->assignRole($userRole);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => JWTAuth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function profile()
    {
        return response()->json(Auth::user());
    }

    // Khusus untuk autentikasi device IoT
    public function deviceLogin(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'secret_key' => 'required|string',
        ]);

        // Cek device di database
        $device = Device::where('unique_id', $request->device_id)
            ->where('secret_key', $request->secret_key)
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Generate token khusus device
        $token = JWTAuth::fromUser($device, ['role' => 'device']);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
