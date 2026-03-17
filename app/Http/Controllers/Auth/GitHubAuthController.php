<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::where('github_id', $githubUser->getId())->first();

        if ($user) {
            $user->update([
                'avatar' => $githubUser->getAvatar(),
            ]);
        } else {
            $user = User::where('email', $githubUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'github_id' => $githubUser->getId(),
                    'avatar' => $githubUser->getAvatar(),
                ]);
            } else {
                $base = Str::slug($githubUser->getNickname() ?: Str::before($githubUser->getEmail(), '@'));
                $username = $base ?: 'user' . Str::random(8);
                $original = $username;
                $i = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $original . $i;
                    $i++;
                }

                $user = User::create([
                    'name' => $githubUser->getName() ?: $githubUser->getNickname(),
                    'username' => $username,
                    'email' => $githubUser->getEmail(),
                    'email_verified_at' => now(),
                    'github_id' => $githubUser->getId(),
                    'avatar' => $githubUser->getAvatar(),
                    'password' => Hash::make(Str::random(64)),
                ]);
                $user->assignRole('student');
            }
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
