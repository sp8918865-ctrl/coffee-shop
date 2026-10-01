<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Warung Luwak Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-900 text-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-2xl">
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo Warung Luwak" class="mb-4 h-32 w-32 rounded-2xl border border-slate-700 object-cover shadow-lg shadow-amber-600/10">
            <h1 class="text-2xl font-bold text-white">Warung Luwak</h1>
            <p class="text-slate-400 text-sm">Console POS System</p>
        </div>

        @if (app()->environment('local'))
            <p class="mb-5 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-xs text-amber-200">Mode development: email dan kata sandi tidak diperiksa.</p>
        @endif

        @if ($errors->any())
            <div role="alert" class="mb-5 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first() }}</div>
        @endif
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Email</label>
                <div class="relative">
                    <i data-lucide="mail" class="w-5 h-5 text-slate-500 absolute left-3.5 top-3.5"></i>
                    <input name="email" type="text" autocomplete="username" value="{{ old('email') }}" class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-amber-500 text-sm transition" placeholder="Email apa saja">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Kata Sandi</label>
                <div class="relative">
                    <i data-lucide="lock" class="w-5 h-5 text-slate-500 absolute left-3.5 top-3.5"></i>
                    <input name="password" type="password" autocomplete="current-password" class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-amber-500 text-sm transition" placeholder="Boleh dikosongkan di mode development">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-500 font-semibold rounded-xl text-white transition shadow-lg shadow-amber-600/20 flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i> Masuk ke Sistem
            </button>
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>