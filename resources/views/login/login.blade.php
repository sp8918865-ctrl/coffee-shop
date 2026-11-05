<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login {{ $loginRole === 'barista' ? 'Barista' : 'Admin' }} - Warung Luwak Console</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-2xl">
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo Warung Luwak" class="mb-4 h-32 w-32 rounded-2xl border border-slate-700 object-cover shadow-lg shadow-amber-600/10">
            <h1 class="text-2xl font-bold text-white">Warung Luwak</h1>
            <p class="text-slate-400 text-sm">{{ $loginRole === 'barista' ? 'Login Barista' : 'Login Admin' }}</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-5 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first() }}</div>
        @endif
        @if (session('status'))
            <div role="status" class="mb-5 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">{{ session('status') }}</div>
        @endif
        <form action="{{ route($loginRole === 'barista' ? 'login.barista.submit' : 'login.admin.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="username" class="block text-xs font-semibold text-slate-300 mb-2">Username</label>
                <div class="relative">
                    <i data-lucide="user" class="w-5 h-5 text-slate-500 absolute left-3.5 top-3.5"></i>
                    <input id="username" name="username" type="text" autocomplete="username" value="{{ old('username') }}" required class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-amber-500 text-sm transition" placeholder="username">
                </div>
                @error('username')<p class="mt-2 text-xs text-rose-300">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 mb-2">Kata Sandi</label>
                <div class="relative">
                    <i data-lucide="lock" class="w-5 h-5 text-slate-500 absolute left-3.5 top-3.5"></i>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-amber-500 text-sm transition" placeholder="Masukkan kata sandi">
                    @error('password')<p class="mt-2 text-xs text-rose-300">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-500 font-semibold rounded-xl text-white transition shadow-lg shadow-amber-600/20 flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i> Masuk {{ $loginRole === 'barista' ? 'sebagai Barista' : 'sebagai Admin' }}
            </button>
        </form>
        <a
            href="{{ route($loginRole === 'barista' ? 'login.admin' : 'login.barista') }}"
            class="mt-4 block text-center text-xs text-slate-400 underline decoration-slate-600 underline-offset-4 transition hover:text-amber-300"
        >
            Masuk sebagai {{ $loginRole === 'barista' ? 'Admin' : 'Barista' }}
        </a>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>