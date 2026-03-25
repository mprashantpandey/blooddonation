@extends('admin.layout')

@section('title', 'Profile')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Profile',
        'description' => 'Update your admin name and email. Use the Password page to change your login password.',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-white/70 p-6 shadow-lg">
        <form method="post" action="{{ route('admin.profile.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-zinc-600">Name</label>
                <input
                    name="name"
                    value="{{ old('name', $admin?->name) }}"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100"
                    placeholder="Administrator"
                    required
                />
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-zinc-600">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $admin?->email) }}"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100"
                    placeholder="admin@example.com"
                    required
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a
                    href="{{ route('admin.profile.password') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-700 transition hover:border-zinc-300 hover:text-zinc-900"
                >
                    Change password
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500"
                >
                    Save profile
                </button>
            </div>
        </form>
    </div>
@endsection

