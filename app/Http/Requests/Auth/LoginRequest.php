<?php

namespace App\Http\Requests\Auth;

use App\Rules\UmakEmail;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', new UmakEmail],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            $existingUser = \App\Models\User::where('email', strtolower($this->string('email')))->first();
            if ($existingUser && $existingUser->google_id) {
                throw ValidationException::withMessages([
                    'email' => 'These credentials do not match. Since this account was linked with Google, please sign in with Google or use "Forgot your password?" below to create a manual login password.',
                ]);
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Check account status after successful credential match
        $user = Auth::user();
        if ($user->account_status === \App\Models\User::STATUS_SUSPENDED) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());
            session()->flash('account_error', 'Your account has been suspended. Please contact the COMSOC administrator for assistance.');
            throw ValidationException::withMessages([
                'email' => ' ', // keeps the field highlighted without duplicating the message
            ]);
        }

        if ($user->account_status === \App\Models\User::STATUS_INACTIVE) {
            Auth::logout();
            session()->flash('account_error', 'Your account is inactive. Please contact the COMSOC administrator to reactivate your access.');
            throw ValidationException::withMessages([
                'email' => ' ',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        session()->flash('login_locked_seconds', $seconds);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
