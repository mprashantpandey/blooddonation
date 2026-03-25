@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Dashboard',
        'description' => 'Quick snapshot of registered users, donors, and requests. Tune the mobile experience under Mobile app in the sidebar.',
    ])

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            'Users' => $counts['users'],
            'Donors' => $counts['donors'],
            'Blood requests' => $counts['requests'],
            'Cities' => $counts['cities'],
        ] as $label => $value)
            <div class="rounded-2xl border border-zinc-200/80 bg-white/70 p-5 shadow-lg transition hover:border-zinc-300/80">
                <div class="text-xs font-semibold uppercase tracking-widest text-zinc-500">{{ $label }}</div>
                <div class="mt-3 text-3xl font-semibold tabular-nums text-zinc-900">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-zinc-200/80 bg-gradient-to-br from-zinc-50 to-zinc-100 p-6">
            <h2 class="text-sm font-semibold text-zinc-900">Live app name</h2>
            <p class="mt-2 text-2xl font-medium text-red-600">{{ $settings->app_name }}</p>
            <p class="mt-4 text-sm leading-relaxed text-zinc-500">Branding, welcome dialog, modules, sign-in rules, and optional Firebase JSON are edited on dedicated pages so nothing is buried in one long form.</p>
            <a href="{{ route('admin.settings.branding') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-red-400 hover:text-red-300">
                Open branding
                <span aria-hidden="true">→</span>
            </a>
        </div>
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-6">
            <h2 class="text-sm font-semibold text-zinc-900">Mobile bootstrap</h2>
            <p class="mt-2 text-sm text-zinc-600">The app calls</p>
            <code class="mt-2 block rounded-xl bg-zinc-100 px-3 py-2 text-xs text-red-700">GET /api/v1/bootstrap</code>
            <p class="mt-3 text-sm text-zinc-500">Use HTTPS in production and set the app’s API base URL at build or runtime.</p>
        </div>
    </div>
@endsection
