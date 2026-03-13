<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Install') – LearnFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#F5F4F0',
                        surface: '#FFFFFF',
                        ink: '#0E0E0C',
                        ink2: '#5A5A56',
                        rule: '#E0DFD8',
                        accent: '#1A43E0',
                    },
                    fontFamily: {
                        display: ['Syne', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    borderRadius: {
                        card: '6px',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-bg font-body text-ink antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6">
        {{-- Logo / Header --}}
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-display font-extrabold text-ink tracking-tight">
                Learn<span class="text-accent">Flow</span>
            </h1>
            <p class="text-[13px] text-ink2 font-medium mt-2 uppercase tracking-widest opacity-70">Installation Wizard</p>
        </div>

        {{-- Progress steps --}}
        @hasSection('steps')
        <nav class="mb-8 flex items-center gap-3 text-[12px] font-bold uppercase tracking-wider text-ink3" aria-label="Installation progress">
            @yield('steps')
        </nav>
        @endif

        {{-- Main card --}}
        <div class="w-full max-w-lg bg-surface/80 backdrop-blur-xl rounded-card shadow-sm border border-rule overflow-hidden">
            <div class="p-10 text-center">
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-card bg-red-50 border border-red-100 text-red-600 text-[13px] font-medium leading-relaxed">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <p class="mt-8 text-xs text-slate-400">LearnFlow &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
