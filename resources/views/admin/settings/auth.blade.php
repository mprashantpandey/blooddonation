@extends('admin.layout')

@section('title', 'Phone sign-in')

@php
    $inp = 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
    $lbl = 'mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500';
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Phone sign-in',
        'description' => 'Rules the mobile client should enforce before Firebase phone OTP. Actual SMS is still configured in the Firebase console.',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <form method="post" action="{{ route('admin.settings.auth.update') }}" class="max-w-md space-y-6">
            @csrf
            @method('PUT')

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-zinc-200/60 bg-white/60 p-4 text-sm text-zinc-700">
                <input type="hidden" name="auth_require_phone_verification" value="0" />
                <input type="checkbox" name="auth_require_phone_verification" value="1" @checked(old('auth_require_phone_verification', $settings->auth_require_phone_verification)) class="mt-0.5 h-4 w-4 rounded border-zinc-300 bg-white text-red-600 focus:ring-red-500" />
                <span>
                    <span class="font-medium text-zinc-900">Require phone verification</span>
                    <span class="mt-1 block text-zinc-500">When on, the app should block unverified users from core flows.</span>
                </span>
            </label>

            <div>
                <label class="{{ $lbl }}">Minimum phone digits</label>
                <input type="number" name="auth_min_phone_digits" min="6" max="15" value="{{ old('auth_min_phone_digits', $settings->auth_min_phone_digits) }}" required class="{{ $inp }}" />
            </div>

            <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Save sign-in rules</button>
        </form>
    </div>
@endsection
