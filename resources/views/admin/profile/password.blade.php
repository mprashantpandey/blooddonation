@extends('admin.layout')

@section('title', 'Password')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Change password',
        'description' => 'Use a strong password. You will stay signed in after updating it.',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-white/70 p-6 shadow-lg">
        <form method="post" action="{{ route('admin.profile.password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-zinc-600">Current password</label>
                <input
                    type="password"
                    name="current_password"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100"
                    required
                />
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-zinc-600">New password</label>
                <input
                    type="password"
                    name="password"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100"
                    minlength="8"
                    required
                />
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-zinc-600">Confirm new password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100"
                    minlength="8"
                    required
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a
                    href="{{ route('admin.profile.edit') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-700 transition hover:border-zinc-300 hover:text-zinc-900"
                >
                    Back to profile
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500"
                >
                    Update password
                </button>
            </div>
        </form>
    </div>
@endsection

