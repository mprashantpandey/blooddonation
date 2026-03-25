<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin sign in — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 px-4 py-10 text-zinc-900 antialiased sm:px-6 sm:py-16">
    <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-md flex-col justify-center">
        <div class="mb-8 text-center sm:mb-10">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-600/20 text-2xl font-bold text-red-400">B</div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 sm:text-3xl">Admin sign in</h1>
            <p class="mt-2 text-sm text-zinc-500 sm:text-base">Blood donation control panel</p>
        </div>

        <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-2xl shadow-zinc-900/5 sm:p-8">
            <form method="post" action="{{ route('admin.login.attempt') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-3 text-sm text-zinc-900 shadow-inner placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20" />
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-3 text-sm text-zinc-900 shadow-inner focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20" />
                </div>
                <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-600">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-zinc-600 bg-zinc-900 text-red-600 focus:ring-red-500" />
                    Stay signed in on this device
                </label>
                @error('email')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
                <button type="submit" class="w-full rounded-xl bg-red-600 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/40 transition hover:bg-red-500">Sign in</button>
            </form>
        </div>

        <p class="mt-8 text-center text-xs text-zinc-600">Use a strong password in production and change the seeded account.</p>
    </div>
</body>
</html>
