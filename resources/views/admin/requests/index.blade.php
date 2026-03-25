@extends('admin.layout')

@section('title', 'Blood Requests')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Blood requests',
        'description' => 'Review all requests and update status to open, closed, or fulfilled.',
    ])

    <div class="mb-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 sm:p-6">
        <form method="get" class="flex items-center gap-3">
            <select name="status" class="rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                <option value="" @selected($statusFilter === '')>all statuses</option>
                <option value="open" @selected($statusFilter === 'open')>open</option>
                <option value="closed" @selected($statusFilter === 'closed')>closed</option>
                <option value="fulfilled" @selected($statusFilter === 'fulfilled')>fulfilled</option>
            </select>
            <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-500">Filter</button>
        </form>
    </div>

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 shadow-xl sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm datatable">
                <thead class="text-left text-zinc-600">
                    <tr class="border-b border-zinc-200">
                        <th class="pb-3 pr-4">Request</th>
                        <th class="pb-3 pr-4">Requester</th>
                        <th class="pb-3 pr-4">City</th>
                        <th class="pb-3 pr-4">Responses</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/80">
                    @forelse ($requests as $requestModel)
                        <tr>
                            <td class="py-3 pr-4">
                                <div class="font-medium text-zinc-900">{{ $requestModel->blood_group }} — {{ $requestModel->patient_name }}</div>
                                <div class="text-xs text-zinc-500">{{ $requestModel->hospital }}</div>
                                @if ($requestModel->message)
                                    <div class="mt-1 text-xs text-zinc-500">{{ $requestModel->message }}</div>
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('admin.requests.show', $requestModel) }}" class="inline-flex items-center gap-1 rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-semibold text-zinc-800 hover:border-zinc-300">
                                        View details
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-zinc-700">{{ $requestModel->user?->name ?? 'Unknown' }}</td>
                            <td class="py-3 pr-4 text-zinc-700">{{ $requestModel->city?->city_name ?? '—' }}</td>
                            <td class="py-3 pr-4">
                                @php
                                    $interestedCount = (int) ($requestModel->interested_count ?? 0);
                                    $ignoredCount = (int) ($requestModel->ignored_count ?? 0);
                                @endphp
                                <div class="flex flex-wrap gap-2 text-xs">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 font-semibold text-emerald-700">Interested: {{ $interestedCount }}</span>
                                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-1 font-semibold text-zinc-700">Ignored: {{ $ignoredCount }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <form method="post" action="{{ route('admin.requests.status.update', $requestModel) }}" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900">
                                        <option value="open" @selected($requestModel->status === 'open')>open</option>
                                        <option value="closed" @selected($requestModel->status === 'closed')>closed</option>
                                        <option value="fulfilled" @selected($requestModel->status === 'fulfilled')>fulfilled</option>
                                    </select>
                                    <button class="rounded-lg border border-zinc-200 px-3 py-1.5 text-xs font-semibold text-zinc-800 hover:border-zinc-300">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500">No requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    </div>
@endsection

