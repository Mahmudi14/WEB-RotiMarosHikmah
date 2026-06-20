<?php

namespace App\Http\Requests\Auth;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $email = (string) $this->input('email');

        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            $this->recordFailedLogin(
                email: $email,
                user: null,
            );

            throw ValidationException::withMessages([
                'email' => 'Akun belum terdaftar.',
            ]);
        }

        if (! Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            $this->recordFailedLogin(
                email: $user->email,
                user: $user,
            );

            throw ValidationException::withMessages([
                'password' => 'Password salah.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $this->recordFailedLogin(
            email: (string) $this->input('email'),
            user: User::query()->where('email', $this->input('email'))->first(),
        );

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }

    private function recordFailedLogin(string $email, ?User $user): void
    {
        LoginHistory::create([
            'user_id' => $user?->id,
            'email' => $email,
            'user_name' => $user?->name,
            'user_role' => $user?->role,
            'status' => 'failed',
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'logged_at' => now(),
        ]);
    }
}