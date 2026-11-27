<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Handles public registration, session login, and logout for all account types. */
class AuthController extends Controller
{
    /** TODO: Display the sign-in form and return the auth.login view. */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /** TODO: Validate credentials, authenticate an active account, and regenerate its session. */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([...$credentials, 'status' => true], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Thông tin đăng nhập không hợp lệ.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /** TODO: Display the registration form; role choices are restricted to parent or tutor. */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /** TODO: Validate input, create the user and role atomically, and create a pending tutor profile if needed. */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['parent', 'tutor'])],
        ]);

        $user = DB::transaction(function () use ($data): User {
            $role = Role::where('slug', $data['role'])->firstOrFail();
            unset($data['role']);
            $user = User::create($data);
            $user->roles()->attach($role);

            if ($data['role'] === 'tutor') {
                TutorProfile::create(['user_id' => $user->id]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /** TODO: Invalidate the session and regenerate the CSRF token before redirecting. */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
