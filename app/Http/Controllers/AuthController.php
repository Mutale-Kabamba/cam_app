<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the unified login portal.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isJudge()) {
                return redirect()->route('judge.index');
            }
            if ($user->isAdmin()) {
                return redirect('/admin');
            }
            return redirect()->route('program.index');
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication for Admins & Judges.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isJudge()) {
                $judgeName = $user->getJudgeName();
                return redirect()->intended(route('judge.index'))
                    ->with('success', "Welcome to the Adjudication Workstation, {$judgeName}!");
            }

            if ($user->isAdmin()) {
                return redirect()->intended('/admin')
                    ->with('success', "Welcome back, {$user->name}!");
            }

            return redirect()->intended(route('program.index'))
                ->with('success', "Welcome back, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our official records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('program.index')
            ->with('success', 'You have been successfully logged out.');
    }
}
