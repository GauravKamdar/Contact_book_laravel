<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required|digits:10',
            'about' => 'nullable|string',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle file upload
        $avatarName = null;
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = time() . '.' . $avatarFile->getClientOriginalExtension(); // Generate a unique file name
            $avatarFile->storeAs('avatars', $avatarName, 'public'); // Store in 'avatars' directory
        }

        // Create user
        User::create([
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'about' => $request->about,
            'address' => $request->address,
            'avatar' => $avatarName,
        ]);
        // Redirect to the login page after successful registration
        return redirect('/')->with('success', 'Registration successful! Please log in.');
    }
}

