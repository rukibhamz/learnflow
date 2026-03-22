@extends('layouts.admin')

@section('title', 'Hero Section Management')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold font-display text-ink">Hero Slider</h1>
    </div>

    <div class="space-y-8">
        <div class="bg-surface border border-rule rounded-xl overflow-hidden shadow-sm">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div class="p-8 space-y-8">
                    <div class="flex items-center justify-between border-b border-rule pb-3">
                        <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3">Slider Settings</h3>
                        <p class="text-[11px] text-ink3">Global settings for the hero slider behavior.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                        <!-- Autoplay Speed -->
                        <div class="space-y-2">
                            <label class="block text-[13px] font-bold text-ink font-poppins">Autoplay Speed (ms)</label>
                            <p class="text-[11px] text-ink3 font-sans leading-relaxed mb-2">Duration between slides in milliseconds (e.g. 5000 for 5 seconds).</p>
                            <input type="number" name="hero_autoplay_speed" value="{{ \App\Models\Setting::get('hero_autoplay_speed', '6000') }}" 
                                   class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow">
                        </div>

                        <!-- Slider Animations -->
                        <div class="flex items-center justify-between pt-6 md:pt-8">
                            <div class="space-y-1">
                                <span class="text-[13px] font-bold text-ink font-poppins">Enable Visual Effects</span>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Toggle premium entrance animations for slider content.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="hero_animations_enabled" value="0">
                                <input type="checkbox" name="hero_animations_enabled" value="1" {{ \App\Models\Setting::get('hero_animations_enabled', '1') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-rule/50">
                        <button type="submit" class="px-8 py-2.5 bg-ink text-white rounded-lg font-poppins font-bold text-[11px] uppercase tracking-widest hover:bg-ink/90 transition-all shadow-lg shadow-black/5">
                            Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @livewire('admin-hero-slides')
    </div>
@endsection
