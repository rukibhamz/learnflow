@extends('layouts.app')

@section('title', 'Pricing')

@section('content')
<div class="min-h-screen bg-bg">
    <div class="bg-ink text-white py-16 text-center">
        <h1 class="font-display font-extrabold text-4xl md:text-5xl mb-4">Choose Your Plan</h1>
        <p class="text-lg text-white/80 max-w-2xl mx-auto">Get unlimited access to courses with a subscription, or purchase courses individually.</p>

        <div class="mt-8 flex justify-center" x-data="{ cycle: 'monthly' }">
            <div class="inline-flex bg-white/10 rounded-full p-1">
                <button @click="cycle = 'monthly'" :class="cycle === 'monthly' ? 'bg-white text-ink' : 'text-white/70'"
                    class="px-6 py-2 text-sm font-medium rounded-full transition-all">Monthly</button>
                <button @click="cycle = 'yearly'" :class="cycle === 'yearly' ? 'bg-white text-ink' : 'text-white/70'"
                    class="px-6 py-2 text-sm font-medium rounded-full transition-all">
                    Yearly <span class="text-green-400 text-xs font-bold">Save up to 30%</span>
                </button>
            </div>

            <input type="hidden" x-model="cycle" id="billing-cycle">
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 -mt-10 pb-20" x-data="{ cycle: 'monthly' }" x-init="$watch('cycle', val => document.getElementById('billing-cycle').value = val)">
        <div class="grid grid-cols-1 md:grid-cols-{{ min(count($plans), 3) }} gap-6">
            @foreach($plans as $plan)
                <div class="bg-surface border {{ $plan->sort_order === 1 ? 'border-primary ring-2 ring-primary/20' : 'border-rule' }} rounded-2xl p-8 flex flex-col relative">
                    @if($plan->sort_order === 1)
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-primary text-white text-xs font-bold rounded-full">Most Popular</span>
                    @endif

                    <h3 class="font-display font-bold text-xl text-ink mb-2">{{ $plan->name }}</h3>
                    <p class="text-sm text-ink3 mb-6">{{ $plan->description }}</p>

                    <div class="mb-6">
                        <template x-if="cycle === 'monthly'">
                            <div>
                                <span class="font-display font-extrabold text-4xl text-ink">{{ $plan->formattedMonthlyPrice() }}</span>
                                <span class="text-sm text-ink3">/month</span>
                            </div>
                        </template>
                        <template x-if="cycle === 'yearly'">
                            <div>
                                @if($plan->price_yearly)
                                    <span class="font-display font-extrabold text-4xl text-ink">{{ $plan->formattedYearlyPrice() }}</span>
                                    <span class="text-sm text-ink3">/year</span>
                                    @if($plan->yearlyMonthlySavings())
                                        <span class="ml-2 text-xs font-bold text-green-600">Save {{ $plan->yearlyMonthlySavings() }}%</span>
                                    @endif
                                @else
                                    <span class="font-display font-extrabold text-4xl text-ink">{{ $plan->formattedMonthlyPrice() }}</span>
                                    <span class="text-sm text-ink3">/month</span>
                                @endif
                            </div>
                        </template>
                    </div>

                    <ul class="space-y-3 mb-8 flex-1">
                        @foreach($plan->features ?? [] as $feature)
                            <li class="flex items-start gap-2 text-sm text-ink2">
                                <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                                {{ $feature }}
                            </li>
                        @endforeach
                        @if($plan->course_limit)
                            <li class="flex items-start gap-2 text-sm text-ink2">
                                <span class="material-symbols-outlined text-primary text-[18px] mt-0.5">info</span>
                                Up to {{ $plan->course_limit }} courses
                            </li>
                        @else
                            <li class="flex items-start gap-2 text-sm text-ink2">
                                <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">all_inclusive</span>
                                Unlimited courses
                            </li>
                        @endif
                    </ul>

                    @auth
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <span class="w-full py-3 bg-green-50 text-green-700 text-sm font-bold text-center rounded-xl">Current Plan</span>
                        @else
                            <form method="POST" action="{{ route('subscription.subscribe', $plan) }}">
                                @csrf
                                <input type="hidden" name="billing_cycle" x-bind:value="cycle">
                                <button type="submit"
                                    class="w-full py-3 {{ $plan->sort_order === 1 ? 'bg-primary text-white' : 'bg-ink text-white' }} font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity">
                                    Subscribe
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full py-3 bg-ink text-white font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity text-center block">
                            Sign In to Subscribe
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>

        @auth
            @if(auth()->user()->subscribed('default'))
                <div class="mt-10 text-center">
                    <a href="{{ route('subscription.portal') }}" class="text-sm text-primary hover:underline">Manage Subscription &rarr;</a>
                </div>
            @endif
        @endauth
    </div>
</div>
@endsection
