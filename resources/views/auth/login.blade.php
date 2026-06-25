<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk &middot; {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-full items-center justify-center bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-700 p-4">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center text-white">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-2xl font-bold">H</div>
            <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
            <p class="text-sm text-teal-100">Sistem Informasi Travel Haji &amp; Umroh</p>
        </div>

        <div class="rounded-2xl bg-white p-8 shadow-xl">
            <h2 class="mb-1 text-lg font-semibold text-slate-800">Selamat datang kembali</h2>
            <p class="mb-6 text-sm text-slate-500">Masuk untuk mengelola travel Anda.</p>

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    Ingat saya
                </label>
                <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700">
                    Masuk
                </button>
            </form>
        </div>
        <p class="mt-6 text-center text-xs text-teal-100">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
</body>
</html>
