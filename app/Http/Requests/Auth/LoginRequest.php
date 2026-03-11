<?php

namespace App\Http\Requests\Auth;

use App\Services\ProgressiveLoginLockoutService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Failed attempt - record it with progressive lockout
            $this->recordFailedAttempt();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Successful login - clear failed attempts
        ProgressiveLoginLockoutService::clearAttempts($this->email, $this->ip());
    }

    /**
     * Ensure the login request is not rate limited with progressive lockout.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Check if account is in progressive lockout
        $lockoutStatus = ProgressiveLoginLockoutService::isLockedOut($this->email, $this->ip());
        
        if ($lockoutStatus['locked']) {
            event(new Lockout($this));
            
            throw ValidationException::withMessages([
                'email' => $lockoutStatus['message'],
            ])->status(429);
        }
    }

    /**
     * Record a failed login attempt and handle progressive lockout
     */
    protected function recordFailedAttempt(): void
    {
        // Record the failed attempt with progressive lockout
        $result = ProgressiveLoginLockoutService::recordFailedAttempt($this->email, $this->ip());

        if (!$result) {
            return; // Database error, continue normally
        }

        // If we're past the warning threshold, show appropriate message
        if ($result['attempts'] > ProgressiveLoginLockoutService::getWarningThreshold()) {
            // We're in lockout zone - show warning about remaining lockout
            if ($result['warning']) {
                session()->flash('lockout_warning', $result['warning']);
            }
        } elseif ($result['attempts'] == ProgressiveLoginLockoutService::getWarningThreshold()) {
            // Show warning before lockout
            session()->flash('lockout_warning', $result['warning']);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // This method is kept for potential future use but not actively used in progressive lockout
        return strtolower($this->email) . '|' . $this->ip();
    }
}
