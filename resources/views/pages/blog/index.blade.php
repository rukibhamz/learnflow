@extends('layouts.app')

@section('title', 'Blog - LearnFlow Academy')

@section('content')
<div class="bg-surface py-20 border-b border-rule">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-display font-bold text-ink mb-6">LearnFlow <span class="text-primary tracking-tighter">Insights</span></h1>
        <p class="text-lg text-ink2 max-w-2xl mx-auto font-body leading-relaxed">
            Stay updated with the latest trends, tips, and stories from the world of online learning and professional growth.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @forelse($posts as $post)
            <article class="flex flex-col group h-full bg-surface border border-rule rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video overflow-hidden">
                    @if($post->getFirstMediaUrl('featured_image'))
                        <img src="{{ $post->getFirstMediaUrl('featured_image') }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-primary/5 flex items-center justify-center text-primary/40">
                             <span class="material-symbols-outlined text-[48px]">article</span>
                        </div>
                    @endif
                </a>
                
                <div class="p-8 flex flex-col flex-1">
                    <div class="flex items-center gap-4 mb-4 font-poppins font-bold text-[10px] uppercase tracking-widest text-ink3">
                        <span>{{ $post->published_at?->format('M j, Y') ?? 'Recently' }}</span>
                        <span class="w-1 h-1 bg-rule rounded-full"></span>
                        <span class="text-primary">Blog</span>
                    </div>
                    
                    <h2 class="text-xl font-display font-bold text-ink mb-4 group-hover:text-primary transition-colors leading-snug">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    
                    <p class="text-[14px] text-ink2 font-body leading-relaxed mb-6 line-clamp-3">
                        {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}
                    </p>
                    
                    <div class="mt-auto pt-6 border-t border-rule flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center overflow-hidden">
                                <span class="text-primary font-bold text-[10px]">A</span>
                            </div>
                            <span class="text-[12px] font-bold text-ink">Author</span>
                        </div>
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-[12px] font-bold text-primary flex items-center gap-1 group/btn hover:underline transition-all">
                            Read More
                            <span class="material-symbols-outlined text-[16px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-background-light rounded-full flex items-center justify-center mx-auto mb-6 text-ink3">
                    <span class="material-symbols-outlined text-[40px]">article</span>
                </div>
                <h3 class="text-xl font-bold text-ink mb-2">No posts found</h3>
                <p class="text-ink3">We're still writing some great content. Check back soon!</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-16">
        {{ $posts->links() }}
    </div>
</div>
@endsection
