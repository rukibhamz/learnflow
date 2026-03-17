<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $bio = '';
    public string $website = '';
    public string $twitter = '';
    public string $linkedin = '';
    public string $github = '';
    public $avatar;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->bio = $user->bio ?? '';
        $this->website = $user->website ?? '';

        $links = $user->social_links ?? [];
        $this->twitter = $links['twitter'] ?? '';
        $this->linkedin = $links['linkedin'] ?? '';
        $this->github = $links['github'] ?? '';
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'github' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->avatar) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($this->avatar->getRealPath())
                ->usingFileName('avatar-' . $user->id . '.' . $this->avatar->getClientOriginalExtension())
                ->toMediaCollection('avatar');
            $user->avatar = null;
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'bio' => $validated['bio'],
            'website' => $validated['website'],
            'social_links' => array_filter([
                'twitter' => $validated['twitter'],
                'linkedin' => $validated['linkedin'],
                'github' => $validated['github'],
            ]),
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $this->avatar = null;

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();
        $user->clearMediaCollection('avatar');
        $user->update(['avatar' => null]);
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        {{-- Avatar --}}
        <div>
            <x-input-label :value="__('Profile Photo')" />
            <div class="mt-2 flex items-center gap-6">
                <div class="shrink-0">
                    @if ($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" class="h-16 w-16 rounded-full object-cover" alt="Preview" />
                    @else
                        <img src="{{ auth()->user()->avatar_url }}" class="h-16 w-16 rounded-full object-cover" alt="{{ auth()->user()->name }}" />
                    @endif
                </div>
                <div class="flex flex-col gap-2">
                    <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                        <span>{{ __('Choose Photo') }}</span>
                        <input type="file" wire:model="avatar" accept="image/jpeg,image/png,image/webp" class="hidden" />
                    </label>
                    @if (auth()->user()->getFirstMedia('avatar'))
                        <button type="button" wire:click="removeAvatar" class="text-xs text-red-600 hover:text-red-800 text-left">
                            {{ __('Remove photo') }}
                        </button>
                    @endif
                    <p class="text-xs text-gray-500">{{ __('JPG, PNG or WEBP. Max 2MB.') }}</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Bio --}}
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea wire:model="bio" id="bio" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Tell us about yourself...') }}"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        {{-- Website --}}
        <div>
            <x-input-label for="website" :value="__('Website')" />
            <x-text-input wire:model="website" id="website" type="url" class="mt-1 block w-full" placeholder="https://example.com" />
            <x-input-error class="mt-2" :messages="$errors->get('website')" />
        </div>

        {{-- Social Links --}}
        <div>
            <x-input-label :value="__('Social Links')" />
            <div class="mt-2 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-20 text-xs font-medium text-gray-500">Twitter</span>
                    <x-text-input wire:model="twitter" type="text" class="block w-full" placeholder="@username or URL" />
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-20 text-xs font-medium text-gray-500">LinkedIn</span>
                    <x-text-input wire:model="linkedin" type="text" class="block w-full" placeholder="Profile URL" />
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-20 text-xs font-medium text-gray-500">GitHub</span>
                    <x-text-input wire:model="github" type="text" class="block w-full" placeholder="@username or URL" />
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('twitter')" />
            <x-input-error class="mt-2" :messages="$errors->get('linkedin')" />
            <x-input-error class="mt-2" :messages="$errors->get('github')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
