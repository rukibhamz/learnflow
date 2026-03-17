@extends('install.layout')

@section('title', 'Database')

@section('steps')
    <a href="{{ route('install.requirements') }}" class="text-slate-500 hover:text-slate-700">1. Requirements</a>
    <span class="text-slate-300">→</span>
    <span class="text-indigo-600 font-medium">2. Database</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">3. Application</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">4. Install</span>
@endsection

@section('content')
    <h2 class="text-xl font-semibold mb-2">Database configuration</h2>
    <p class="text-slate-600 text-sm mb-6">
        Enter your database credentials. For SQLite, only the file path is needed.
    </p>

    <form method="POST" action="{{ route('install.database.store') }}" id="db-form">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Database type</label>
            <select name="db_connection" id="db_connection"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="sqlite" {{ old('db_connection', 'sqlite') === 'sqlite' ? 'selected' : '' }}>SQLite</option>
                <option value="mysql" {{ old('db_connection') === 'mysql' ? 'selected' : '' }}>MySQL</option>
                <option value="mariadb" {{ old('db_connection') === 'mariadb' ? 'selected' : '' }}>MariaDB</option>
                <option value="pgsql" {{ old('db_connection') === 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
            </select>
        </div>

        {{-- SQLite --}}
        <div id="sqlite-fields" class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Database file path</label>
            <input type="text" name="db_database"
                   value="{{ old('db_database', database_path('database.sqlite')) }}"
                   placeholder="{{ database_path('database.sqlite') }}"
                   class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <p class="mt-1 text-xs text-slate-500">Leave default or enter full path. File will be created if it doesn't exist.</p>
        </div>

        {{-- MySQL / MariaDB / PostgreSQL --}}
        <div id="server-fields" class="space-y-4" style="display: none;">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Host</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Port</label>
                <input type="text" name="db_port" value="{{ old('db_port', '') }}"
                       placeholder="{{ old('db_connection') === 'pgsql' ? '5432' : '3306' }}"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Database name</label>
                <input type="text" name="db_database" value="{{ old('db_database', 'learnflow') }}"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <input type="text" name="db_username" value="{{ old('db_username') }}"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input type="password" name="db_password" value="{{ old('db_password') }}"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="mt-8 flex gap-3">
            <a href="{{ route('install.requirements') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
                Back
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition">
                Test connection &amp; continue
            </button>
        </div>
    </form>

    <script>
        (function() {
            var sel = document.getElementById('db_connection');
            var sqlite = document.getElementById('sqlite-fields');
            var server = document.getElementById('server-fields');
            function toggle() {
                var isSqlite = sel.value === 'sqlite';
                sqlite.style.display = isSqlite ? 'block' : 'none';
                server.style.display = isSqlite ? 'none' : 'block';
            }
            sel.addEventListener('change', toggle);
            toggle();
        })();
    </script>
@endsection
