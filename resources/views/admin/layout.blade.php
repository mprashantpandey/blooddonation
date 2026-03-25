<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- DataTables (desktop table enhancement) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js" defer></script>
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">
        {{-- Mobile header --}}
        <header class="sticky top-0 z-30 flex h-14 shrink-0 items-center justify-between border-b border-zinc-200/80 bg-zinc-50/90 px-4 backdrop-blur-md lg:hidden">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600/20 text-sm font-bold text-red-600">B</span>
                <span class="font-semibold tracking-tight text-zinc-900">Admin</span>
            </div>
            <button type="button" id="admin-nav-open" class="inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white p-2.5 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900" aria-label="Open menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </header>

        <div id="admin-nav-overlay" class="fixed inset-0 z-40 hidden bg-black/30 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

        <aside id="admin-sidebar" data-open="false" class="fixed inset-y-0 left-0 z-50 flex w-[min(18rem,100vw)] -translate-x-full flex-col border-r border-zinc-200/80 bg-white shadow-2xl transition-transform duration-200 ease-out data-[open=true]:translate-x-0 lg:static lg:z-0 lg:w-64 lg:translate-x-0 lg:shadow-none">
            <div class="flex items-center justify-between border-b border-zinc-200/80 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600/20 text-lg font-bold text-red-600">B</span>
                    <div>
                        <div class="text-sm font-semibold text-zinc-900">Blood Donation</div>
                        <div class="text-xs text-zinc-500">Control panel</div>
                    </div>
                </div>
                <button type="button" id="admin-nav-close" class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 lg:hidden" aria-label="Close menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 space-y-0.5 overflow-y-auto p-3">
                @include('admin.partials.nav')
            </nav>

            <form method="post" action="{{ route('admin.logout') }}" class="border-t border-zinc-200/80 p-3">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-xl border border-zinc-200 bg-white/60 px-3 py-2.5 text-left text-sm text-zinc-600 transition hover:border-zinc-300 hover:text-zinc-900">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Log out
                </button>
            </form>
        </aside>

        <main class="min-h-[calc(100vh-3.5rem)] min-w-0 flex-1 lg:min-h-screen">
            <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-10">
                @if (session('status'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
