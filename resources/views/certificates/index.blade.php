@extends('layouts.dashboard')

@section('title', 'My Certificates')
@section('sidebar_nav')
    <a href="http://localhost/learnflow/dashboard" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Dashboard</a>
    <a href="http://localhost/learnflow/certificates" class="block py-2 px-3 text-[13px] rounded-card border-r-2 border-accent pr-2 bg-accent-bg text-accent font-medium">Certificates</a>
    <a href="http://localhost/learnflow/settings" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Settings</a>
@endsection

@section('content')
<h1 class="font-display font-extrabold text-xl text-ink mb-6">My Certificates</h1>
<div class="bg-surface border border-rule rounded-card overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-rule">
                <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase tracking-wider text-ink3">Course</th>
                <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase tracking-wider text-ink3">Date Issued</th>
                <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase tracking-wider text-ink3">ID</th>
                <th class="text-right py-3 px-4 font-display font-bold text-[11px] uppercase tracking-wider text-ink3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach([
                ['course' => 'Web Development Bootcamp', 'date' => 'Mar 10, 2025', 'id' => 'LF-8A2B-4C3D-9E1F'],
                ['course' => 'Data Science Fundamentals', 'date' => 'Feb 28, 2025', 'id' => 'LF-2B3C-5D6E-7F8A'],
            ] as $cert)
            <tr class="border-b border-rule h-12">
                <td class="py-3 px-4 font-body text-[13px] text-ink">{{ $cert['course'] }}</td>
                <td class="py-3 px-4 font-body text-[13px] text-ink2">{{ $cert['date'] }}</td>
                <td class="py-3 px-4 font-body text-[13px] text-ink3 font-mono text-sm">{{ Str::limit($cert['id'], 16) }}</td>
                <td class="py-3 px-4 text-right">
                    <button class="px-3 py-1.5 border border-rule rounded-card text-[12px] font-body hover:border-ink transition-colors duration-150 mr-2">Download PDF</button>
                    <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $cert['id'] }}'); copied = true; setTimeout(() => copied = false, 2000)" class="text-accent text-[12px] font-body hover:underline" x-text="copied ? 'Copied!' : 'Copy link'"></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
