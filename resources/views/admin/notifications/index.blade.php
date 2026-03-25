@extends('admin.layout')

@section('title', 'Notifications')

@php
    $inp = 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
    $lbl = 'mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500';
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Send push notification',
        'description' => 'Broadcast an FCM push notification to users. You can send to all users or only donors (with optional filters).',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <form method="post" action="{{ route('admin.notifications.send') }}" class="grid max-w-4xl gap-6 sm:grid-cols-2">
            @csrf
            @method('POST')

            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">Audience</label>
                <select name="audience" class="{{ $inp }}">
                    <option value="all_users" @selected(old('audience', 'all_users') === 'all_users')>All users</option>
                    <option value="donors" @selected(old('audience') === 'donors')>Only donors</option>
                </select>
                <p class="mt-2 text-xs text-zinc-500">Donor filters below only apply when Audience is set to “Only donors”.</p>
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">Title</label>
                <input name="title" value="{{ old('title') }}" required class="{{ $inp }}" placeholder="e.g. 🚨 Blood donation drive" />
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">Body</label>
                <textarea name="body" rows="4" required class="{{ $inp }}" placeholder="Message shown in the notification">{{ old('body') }}</textarea>
            </div>

            <div>
                <label class="{{ $lbl }}">City (optional)</label>
                <select name="city_id" class="{{ $inp }}">
                    <option value="">All cities</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c->id }}" @selected(old('city_id') == $c->id)>{{ $c->city_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $lbl }}">Blood group (donors only)</label>
                <input name="blood_group" value="{{ old('blood_group') }}" class="{{ $inp }}" placeholder="e.g. A+ / O-" />
            </div>

            <div class="sm:col-span-2">
                <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-zinc-200/60 bg-white/60 p-4 transition hover:border-zinc-300">
                    <input type="hidden" name="only_available_donors" value="0" />
                    <input type="checkbox" name="only_available_donors" value="1" @checked(old('only_available_donors', true)) class="mt-1 h-4 w-4 shrink-0 rounded border-zinc-300 bg-white text-red-600 focus:ring-red-500" />
                    <span>
                        <span class="block font-medium text-zinc-900">Only available donors (donors only)</span>
                        <span class="mt-0.5 block text-sm text-zinc-600">When enabled, donors must have availability on.</span>
                    </span>
                </label>
            </div>

            <div class="sm:col-span-2 flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Send notification</button>
            </div>
        </form>
    </div>
@endsection

