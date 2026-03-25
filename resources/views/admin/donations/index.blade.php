@extends('admin.layout')

@section('title', 'Donation Proofs')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Donation proof approvals',
        'description' => 'Approve or reject donation proofs and assign points to donor wallets.',
    ])

    <div class="mb-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 sm:p-6">
        <form method="get" class="flex items-center gap-3">
            <select name="status" class="rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                <option value="pending" @selected($statusFilter === 'pending')>pending</option>
                <option value="approved" @selected($statusFilter === 'approved')>approved</option>
                <option value="rejected" @selected($statusFilter === 'rejected')>rejected</option>
                <option value="all" @selected($statusFilter === 'all')>all</option>
            </select>
            <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-500">Filter</button>
        </form>
    </div>

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 shadow-xl sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm datatable">
                <thead class="text-left text-zinc-600">
                    <tr class="border-b border-zinc-200">
                        <th class="pb-3 pr-4">Donation</th>
                        <th class="pb-3 pr-4">Donor</th>
                        <th class="pb-3 pr-4">Proof</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/80">
                    @forelse ($donations as $donation)
                        <tr>
                            <td class="py-3 pr-4">
                                <div class="font-medium text-zinc-900">#{{ $donation->id }} · {{ $donation->status }}</div>
                                <div class="text-xs text-zinc-500">
                                    Request #{{ $donation->request_id }} ·
                                    {{ $donation->bloodRequest?->blood_group ?? '?' }}
                                </div>
                                @if ($donation->hospital_name)
                                    <div class="text-xs text-zinc-500">{{ $donation->hospital_name }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-zinc-700">
                                {{ $donation->donor?->user?->name ?? 'Unknown' }}
                                <div class="text-xs text-zinc-500">{{ $donation->donor?->user?->mobile }}</div>
                            </td>
                            <td class="py-3 pr-4">
                                @if ($donation->proof_image)
                                    <a href="{{ $donation->proof_image }}" target="_blank" class="text-xs font-medium text-red-400 hover:text-red-300">Open proof</a>
                                @else
                                    <span class="text-xs text-zinc-500">No proof URL</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <form method="post" action="{{ route('admin.donations.approve', $donation) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input name="points" type="number" min="0" max="10000" value="{{ $donation->status === 'approved' ? $donation->points : 100 }}"
                                            class="w-24 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900" />
                                        <button class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:border-emerald-300">Approve</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.donations.reject', $donation) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:border-red-300">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-zinc-500">No donations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $donations->links() }}</div>
    </div>
@endsection

