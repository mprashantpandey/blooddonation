@php
    $active = fn (string $pattern) => request()->routeIs($pattern)
        ? 'bg-red-500/10 text-red-800 ring-1 ring-red-500/20'
        : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900';
@endphp

<p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Overview</p>
<a href="{{ route('admin.dashboard') }}" class="{{ $active('admin.dashboard') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
    Dashboard
</a>
<a href="{{ \Illuminate\Support\Facades\Route::has('admin.profile.edit') ? route('admin.profile.edit') : url('/admin/profile') }}" class="{{ $active('admin.profile.*') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.5-1.632z"/></svg>
    Profile
</a>

<p class="mb-2 mt-8 px-3 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Operations</p>
<a href="{{ route('admin.cities.index') }}" class="{{ $active('admin.cities.*') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 21l4-4 4 4m0-8l-4-4-4 4m8-6l-4-4-4 4"/></svg>
    Cities
</a>
<a href="{{ route('admin.users.index') }}" class="{{ $active('admin.users.*') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    Users
</a>
<a href="{{ route('admin.requests.index') }}" class="{{ $active('admin.requests.*') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h8m-8 5h8m-8 5h5M4 4h16v16H4V4z"/></svg>
    Blood requests
</a>
<a href="{{ route('admin.donations.index') }}" class="{{ $active('admin.donations.*') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3c4.5 4 6 6.5 6 9a6 6 0 11-12 0c0-2.5 1.5-5 6-9z"/></svg>
    Donation proofs
</a>
<a href="{{ route('admin.city-sliders.index') }}" class="{{ $active('admin.city-sliders.*') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 17h18M6 7v10m12-10v10M9 11h6m-6 3h6"/></svg>
    City sliders
</a>

<p class="mb-2 mt-8 px-3 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Mobile app</p>
<a href="{{ route('admin.settings.branding') }}" class="{{ $active('admin.settings.branding') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    Branding
</a>
<a href="{{ route('admin.settings.welcome') }}" class="{{ $active('admin.settings.welcome') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    Welcome dialog
</a>
<a href="{{ route('admin.settings.features') }}" class="{{ $active('admin.settings.features') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
    Modules
</a>
<a href="{{ route('admin.settings.auth') }}" class="{{ $active('admin.settings.auth') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    Phone sign-in
</a>
<a href="{{ route('admin.settings.firebase') }}" class="{{ $active('admin.settings.firebase') }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition">
    <svg class="h-5 w-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
    Firebase
</a>
