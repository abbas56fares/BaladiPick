<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on role
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended(route('admin.dashboard'));
                case 'shop':
                    return redirect()->intended(route('shop.dashboard'));
                case 'delivery':
                    return redirect()->intended(route('delivery.dashboard'));
                default:
                    Auth::logout();
                    return back()->withErrors(['email' => 'Invalid role.']);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'timezone' => 'required|string|timezone',
            'role' => 'required|in:shop,delivery',
            'vehicle_type' => 'required_if:role,delivery|nullable|in:bike,car,pickup',
            'id_document' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Upload ID document
        $filePath = null;
        if ($request->hasFile('id_document')) {
            $filePath = $request->file('id_document')->store('id_documents', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
                        'timezone' => $validated['timezone'],
            'role' => $validated['role'],
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'verified' => false,
            'id_document_path' => $filePath,
        ]);

        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'shop') {
            return redirect()->route('shop.profile')->with('success', 'Registration successful! Your ID document is pending admin verification. You will be able to create orders once verified.');
        } else {
            return redirect()->route('delivery.dashboard')->with('success', 'Registration successful! Your ID document is pending admin verification. You will be able to accept orders once verified.');
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
