<?php

namespace App\Http\Controllers;

use App\Models\DnsUser;  // Make sure you are using the correct model for your table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Show the registration form
    public function showRegisterForm()
    {
        return view('auth.register');
    }


    public function store(Request $request)
    {

        $request->validate([
            'username' => 'required|string|max:255|unique:dns_users,username|regex:/^[a-z0-9]+$/',
            'email' => 'required|email|max:255|unique:dns_users,email',
            'password' => 'required|string|min:6|confirmed',
            ], [
            'username.regex' => 'The username must only contain lowercase letters.',
            ]);




        DnsUser::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),  // Hash the password before storing
            ]);

            $userDirectory = "/var/www/html/storage/app/coredns/zones/" . $request->username;
            if (!file_exists($userDirectory)) {
                mkdir($userDirectory, 0777, true);  // Create the directory if it doesn't exist
            }

        // Redirect to login page after successful registration
        return redirect('/login')->with('success', 'Registration successful! Please log in.');
    }
}


