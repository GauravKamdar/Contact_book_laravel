<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show Login Page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/home'); // Redirect to home page
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Show Registration Page
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle Registration
    public function register(Request $request)
    {
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user); // Auto-login after registration
        return redirect('/home');
    }

    // Handle Logout
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
