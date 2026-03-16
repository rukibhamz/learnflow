<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Livewire\Actions\Logout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);

        $login = $request->string('login')->trim();
        $throttleKey = str()->transliterate($login->lower() . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => (int) ceil($seconds / 60)]),
            ]);
        }

        $field = filter_var($login->toString(), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $field => $login->toString(),
            'password' => $request->password,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole('instructor')) {
            return redirect()->intended(route('instructor.dashboard'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Logout $logout): RedirectResponse
    {
        $logout();

        return redirect('/');
    }
}
