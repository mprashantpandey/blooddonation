@extends('admin.layout')

@section('title', 'Users')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Users',
        'description' => 'Search users, block/unblock accounts, and enable or disable donor profiles.',
    ])

    <div class="mb-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 sm:p-6">
        <form method="get" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <input name="q" value="{{ $query }}" placeholder="Search name, mobile, email" class="w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20 sm:max-w-md" />
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
                        <th class="pb-3 pr-4">Donor</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/80">
                    @forelse ($users as $user)
                        <tr>
                            <td class="py-3 pr-4">
                                <div class="font-medium text-zinc-900">{{ $user->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $user->mobile }} {{ $user->email ? '· '.$user->email : '' }}</div>
                                @if ($user->is_blocked)
                                    <span class="mt-1 inline-flex rounded-md bg-red-500/10 px-2 py-0.5 text-[11px] font-semibold text-red-700">blocked</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-zinc-700">{{ $user->city?->city_name ?? '—' }}</td>
                            <td class="py-3 pr-4">
                                @if ($user->donor)
                                    <div class="text-zinc-900">{{ $user->donor->blood_group }}</div>
                                    <div class="text-xs {{ $user->donor->is_enabled ? 'text-emerald-600' : 'text-yellow-600' }}">
                                        {{ $user->donor->is_enabled ? 'enabled' : 'disabled' }}
                                    </div>
                                @else
                                    <span class="text-zinc-500">No donor profile</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex flex-wrap gap-2">
                                    <form method="post" action="{{ route('admin.users.block.toggle', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg border border-zinc-300 px-3 py-2 text-xs font-semibold text-zinc-800 hover:border-zinc-400">
                                            {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                                        </button>
                                    </form>
                                    @if ($user->donor)
                                        <form method="post" action="{{ route('admin.users.donor.enabled.toggle', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg border border-zinc-300 px-3 py-2 text-xs font-semibold text-zinc-800 hover:border-zinc-400">
                                                {{ $user->donor->is_enabled ? 'Disable donor' : 'Enable donor' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
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

