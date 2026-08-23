<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirectUrl(route('auth.google.callback'))
            ->with(['hd' => 'umak.edu.ph', 'prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('auth.google.callback'))
                ->user();
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth error: ' . $e->getMessage());

            return redirect()->route('login')->withErrors([
                'email' => 'Unable to authenticate with Google: ' . $e->getMessage(),
            ]);
        }

        $email = strtolower((string) $googleUser->getEmail());

        // Strictly enforce @umak.edu.ph domain
        if (!str_ends_with($email, '@umak.edu.ph')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Only @umak.edu.ph Google accounts are permitted to sign in.',
            ]);
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            // Check account status
            if ($user->account_status === User::STATUS_SUSPENDED) {
                return redirect()->route('login')->with('account_error', 'Your account has been suspended. Please contact the COMSOC administrator for assistance.');
            }

            if ($user->account_status === User::STATUS_INACTIVE) {
                return redirect()->route('login')->with('account_error', 'Your account is inactive. Please contact the COMSOC administrator to reactivate your access.');
            }

            // Link google_id or avatar if missing
            $user->google_id = $googleUser->getId();
            if (!$user->avatar && $googleUser->getAvatar()) {
                $user->avatar = $googleUser->getAvatar();
            }
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();
        } else {
            // Register new student user
            $user = User::create([
                'name'              => $googleUser->getName() ?: explode('@', $email)[0],
                'email'             => $email,
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                'password'          => Hash::make(Str::random(32)),
                'role'              => User::ROLE_STUDENT,
                'account_status'    => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]);

            event(new Registered($user));
        }

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
