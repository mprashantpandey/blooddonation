@extends('admin.layout')

@section('title', 'Points')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Points management',
        'description' => 'Search users and add/subtract wallet points (creates an adjustment entry).',
    ])

    <div class="mb-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 sm:p-6">
        <form method="get" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <input name="q" value="{{ $query }}" placeholder="Search name, mobile, email"
                class="w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20 sm:max-w-md" />
            <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-500">Search</button>
        </form>
    </div>

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 shadow-xl sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm datatable">
                <thead class="text-left text-zinc-600">
                    <tr class="border-b border-zinc-200">
                        <th class="pb-3 pr-4">User</th>
                        <th class="pb-3 pr-4">City</th>
                        <th class="pb-3 pr-4">Balance</th>
                        <th class="pb-3">Adjust</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/80">
                    @forelse ($users as $user)
                        <tr>
                            <td class="py-3 pr-4">
                                <div class="font-medium text-zinc-900">{{ $user->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $user->mobile }} {{ $user->email ? '· '.$user->email : '' }}</div>
                            </td>
                            <td class="py-3 pr-4 text-zinc-700">{{ $user->city?->city_name ?? '—' }}</td>
                            <td class="py-3 pr-4 font-semibold tabular-nums text-zinc-900">{{ (int) ($user->points_balance ?? 0) }}</td>
                            <td class="py-3">
                                <form method="post" action="{{ route('admin.points.adjust', $user) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input name="points" type="number" min="-10000" max="10000" step="1" placeholder="+50 / -20"
                                        class="w-28 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900" />
                                    <input name="description" type="text" maxlength="255" placeholder="Reason (optional)"
                                        class="w-52 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900" />
                                    <button class="rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-800 hover:border-zinc-400">Apply</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-zinc-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
@endsection

