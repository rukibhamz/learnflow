@extends('layouts.app')

@section('title', $post->title . ' - LearnFlow Blog')

@push('head')
    @if($post->meta_description)
        <meta name="description" content="{{ $post->meta_description }}">
    @endif
    @if($post->keywords)
        <meta name="keywords" content="{{ $post->keywords }}">
    @endif
@endpush

@section('content')
<article class="bg-surface py-20 lg:py-32">
    <div class="max-w-4xl mx-auto px-6">
        {{-- Metadata & Header --}}
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-4 font-poppins font-bold text-[11px] uppercase tracking-widest text-primary">
                <span>{{ $post->published_at?->format('M j, Y') ?? 'Recently Published' }}</span>
                <span class="w-1.5 h-1.5 bg-rule rounded-full"></span>
                <span class="text-ink3">Educational Insights</span>
            </div>
            
            <h1 class="text-4xl lg:text-5xl font-display font-bold text-ink mb-8 leading-tight">
                {{ $post->title }}
            </h1>
            
            <div class="flex items-center justify-center gap-3 pt-4 border-t border-rule max-w-xs mx-auto">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-primary font-bold text-sm">LF</span>
                </div>
                <div class="text-left">
                    <span class="block text-sm font-bold text-ink">LearnFlow Team</span>
                    <span class="block text-[11px] text-ink3">Academic Content Creator</span>
                </div>
            </div>
        </div>
        
        {{-- Featured Image --}}
        @if($post->getFirstMediaUrl('featured_image'))
            <div class="mb-16 -mx-6 lg:-mx-20">
                <img src="{{ $post->getFirstMediaUrl('featured_image') }}" alt="{{ $post->title }}" class="w-full aspect-[21/9] object-cover rounded-3xl shadow-2xl">
            </div>
        @endif
        
        {{-- Content --}}
        <div class="prose prose-lg max-w-none text-ink font-body leading-relaxed prose-headings:font-display prose-headings:font-bold prose-p:mb-8 prose-img:rounded-2xl">
            {!! $post->content !!}
        </div>
        
        {{-- Footer --}}
        <div class="mt-20 pt-10 border-t border-rule flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="flex items-center gap-4">
                <span class="text-sm font-bold text-ink">Share this post:</span>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 rounded-full bg-background-light flex items-center justify-center text-ink3 hover:bg-primary/10 hover:text-primary transition-all shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">share</span>
                    </a>
                </div>
            </div>
            
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary group transition-all">
                <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Back to Blog
            </a>
        </div>
    </div>
</article>

{{-- Related Section Placeholder --}}
<section class="bg-background-light/50 py-20">
    <div class="max-w-7xl mx-auto px-6">
        <h3 class="text-2xl font-display font-bold text-ink mb-12">More Stories</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            @php 
                $related = \App\Models\BlogPost::where('id', '!=', $post->id)->where('is_published', true)->latest()->take(2)->get();
            @endphp
            @foreach($related as $rel)
                <a href="{{ route('blog.show', $rel->slug) }}" class="flex gap-6 group items-center">
                   <div class="w-32 h-24 shrink-0 rounded-xl overflow-hidden shadow-md">
                        @if($rel->getFirstMediaUrl('featured_image'))
                            <img src="{{ $rel->getFirstMediaUrl('featured_image') }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-primary/10 flex items-center justify-center text-primary/40 text-[12px] font-bold">BLOG</div>
                        @endif
                   </div>
                   <div>
                        <h4 class="font-bold text-ink group-hover:text-primary transition-colors leading-snug mb-2">{{ $rel->title }}</h4>
                        <span class="text-xs text-ink3 font-poppins uppercase tracking-wider">{{ $rel->published_at?->format('M j, Y') }}</span>
                   </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
