<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $isStudent = Str::contains(request()->route()->getName(), 'student');
        $isAdmin = Str::contains(request()->route()->getName(), 'admin');
        if ($isStudent) {
            $this['role'] = '2';
            if (!Auth::attempt($this->only('email', 'password', 'role'), $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        }
        if ($isAdmin) {
            $this['role'] = '1';
            if (!Auth::attempt($this->only('email', 'password', 'role'), $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }


    // public function authenticate()
    // {
    //     $this->ensureIsNotRateLimited();

    //     if (request()->segment(1) == 'admin') {
    //         $this['role'] = 'admin';
    //         if (!Auth::attempt($this->only('email', 'password', 'role'), $this->boolean('remember'))) {
    //             RateLimiter::hit($this->throttleKey());

    //             throw ValidationException::withMessages([
    //                 'email' => trans('auth.failed'),
    //             ]);
    //         }
    //     } else if (request()->segment(1) == 'merchant') {
    //         $this['role'] = 'merchant';
    //         $user = Application::where('merchant_email', $this->email)->first();
    //         if ($user) {
    //             if (!$user->status) {
    //                 throw ValidationException::withMessages([
    //                     'not_verified' => 'Your account is not verified.',
    //                 ]);
    //             }
    //         }

    //         $user = User::where('email', $this->email)->first();
    //         if ($user) {
    //             if (!$user->status) {
    //                 throw ValidationException::withMessages([
    //                     'deactivated' => 'Your account is deactivated. For more information you can contact with taaply administration',
    //                 ]);
    //             }
    //         }

    //         if (!Auth::attempt($this->only('email', 'password', 'role'), $this->boolean('remember'))) {
    //             RateLimiter::hit($this->throttleKey());

    //             throw ValidationException::withMessages([
    //                 'auth_failed' => trans('auth.failed'),
    //             ]);
    //         }

    //         User::where('id', $user->id)->update([
    //             'last_login_time' => now()->toDateTimeString()
    //         ]);
    //     } else {
    //         $this['role'] = 'supplier';
    //         if (!Auth::attempt($this->only('email', 'password', 'role'), $this->boolean('remember'))) {
    //             RateLimiter::hit($this->throttleKey());

    //             throw ValidationException::withMessages([
    //                 'email' => trans('auth.failed'),
    //             ]);
    //         }
    //     }


    //     RateLimiter::clear($this->throttleKey());
    // }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

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
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
