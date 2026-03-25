@extends('admin.layout')

@section('title', 'Referrals')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Referrals',
        'description' => 'Track referral relationships created during signup.',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 shadow-xl sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm datatable">
                <thead class="text-left text-zinc-600">
                    <tr class="border-b border-zinc-200">
                        <th class="pb-3 pr-4">Referrer</th>
                        <th class="pb-3 pr-4">New user</th>
                        <th class="pb-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/80">
                    @forelse ($referrals as $r)
                        <tr>
                            <td class="py-3 pr-4">
                                <div class="font-medium text-zinc-900">{{ $r->referrer?->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-zinc-500">{{ $r->referrer?->mobile }}</div>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="font-medium text-zinc-900">{{ $r->newUser?->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-zinc-500">{{ $r->newUser?->mobile }}</div>
                            </td>
                            <td class="py-3 text-zinc-700">{{ $r->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-zinc-500">No referrals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $referrals->links() }}</div>
    </div>
@endsection

