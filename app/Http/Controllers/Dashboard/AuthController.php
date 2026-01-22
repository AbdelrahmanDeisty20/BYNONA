<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\{loginRequest, profileUser, registerRequest};
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('dashboard.login');
    }

    public function login(loginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return response()->json([
                'status' => true,
                'message' => __('Login successful!'),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => __('Invalid email or password.'),
            'errors' => [
                'email' => [__('Invalid email or password.')]
            ]
        ], 422);
    }

    public function userProfile()
    {
        $user = Auth::user();
        return view('dashboard.user-profile', compact('user'));
    }

    public function profileUpdate(profileUser $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('updated profile successfully')
        ]);
    }

    public function logout()
    {
        $user = Auth::user()->tokens()->delete();
        return redirect()->route('dashboard.login');
    }
}
