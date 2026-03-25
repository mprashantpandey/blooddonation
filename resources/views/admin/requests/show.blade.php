@extends('admin.layout')

@section('title', 'Request details')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Request details',
        'description' => 'View request information and donor responses (interested / ignored).',
    ])

    <div class="mb-6">
        <a href="{{ route('admin.requests.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white/60 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to requests
        </a>
    </div>

    <div class="mb-8 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="text-lg font-semibold text-zinc-900">{{ $requestModel->blood_group }} — {{ $requestModel->patient_name }}</div>
                <div class="mt-1 text-sm text-zinc-600">{{ $requestModel->hospital }}</div>
                <div class="mt-2 text-sm text-zinc-600">City: <span class="font-semibold text-zinc-900">{{ $requestModel->city?->city_name ?? '—' }}</span></div>
                <div class="mt-1 text-sm text-zinc-600">Requester: <span class="font-semibold text-zinc-900">{{ $requestModel->user?->name ?? 'Unknown' }}</span></div>
                @if ($requestModel->message)
                    <div class="mt-3 rounded-xl border border-zinc-200 bg-white/70 p-4 text-sm text-zinc-700">{{ $requestModel->message }}</div>
                @endif
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white/70 p-4 text-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Status</div>
                <div class="mt-1 font-semibold text-zinc-900">{{ $requestModel->status }}</div>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 font-semibold text-emerald-700">Interested: {{ $interested->count() }}</span>
                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-1 font-semibold text-zinc-700">Ignored: {{ $ignored->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
            <div class="mb-4 text-sm font-semibold text-zinc-900">Interested donors</div>
            @if ($interested->isEmpty())
                <div class="text-sm text-zinc-600">No interested responses yet.</div>
            @else
                <div class="space-y-3">
                    @foreach ($interested as $resp)
                        @php
                            $u = $resp->donor?->user;
                        @endphp
                        <div class="rounded-xl border border-zinc-200 bg-white/70 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-zinc-900">{{ $u?->name ?? 'Donor' }}</div>
                                    <div class="mt-1 text-xs text-zinc-600">{{ $u?->city?->city_name ?? '—' }}@if ($u?->area) · {{ $u->area }}@endif</div>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 font-semibold text-red-700">{{ $resp->donor?->blood_group ?? '—' }}</span>
                                        @if ($resp->donor?->is_verified)
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 font-semibold text-emerald-700">Verified</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right text-xs text-zinc-500">
                                    <div>Responded</div>
                                    <div class="font-semibold text-zinc-700">{{ $resp->created_at?->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                            @if ($u && $u->badges && $u->badges->count() > 0)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($u->badges as $b)
                                        <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-1 text-xs font-semibold text-zinc-700">{{ $b->badge_name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
            <div class="mb-4 text-sm font-semibold text-zinc-900">Ignored</div>
            @if ($ignored->isEmpty())
                <div class="text-sm text-zinc-600">No ignored responses.</div>
            @else
                <div class="space-y-3">
                    @foreach ($ignored as $resp)
                        @php
                            $u = $resp->donor?->user;
                        @endphp
                        <div class="rounded-xl border border-zinc-200 bg-white/70 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-zinc-900">{{ $u?->name ?? 'Donor' }}</div>
                                    <div class="mt-1 text-xs text-zinc-600">{{ $u?->city?->city_name ?? '—' }}@if ($u?->area) · {{ $u->area }}@endif</div>
                                </div>
                                <div class="text-right text-xs text-zinc-500">
                                    <div>Responded</div>
                                    <div class="font-semibold text-zinc-700">{{ $resp->created_at?->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

