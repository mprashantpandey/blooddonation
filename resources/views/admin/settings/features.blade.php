@extends('admin.layout')

@section('title', 'Modules')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Modules & features',
        'description' => 'Turn sections of the mobile app on or off. The app reads these flags from the bootstrap API and can hide navigation or screens accordingly.',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <form method="post" action="{{ route('admin.settings.features.update') }}" class="max-w-3xl space-y-4">
            @csrf
            @method('PUT')

            @foreach ([
                'feature_chat_enabled' => ['Chat', 'Real-time messaging between requester and donor.'],
                'feature_referrals_enabled' => ['Referrals', 'Refer & earn flows.'],
                'feature_wallet_enabled' => ['Wallet', 'Points balance and history.'],
                'feature_redeem_enabled' => ['Redeem', 'Redeem / rewards screen (e.g. “Coming soon”).'],
            ] as $field => [$label, $help])
                <label class="flex cursor-pointer gap-4 rounded-xl border border-zinc-200/60 bg-white/60 p-4 transition hover:border-zinc-300">
                    <input type="hidden" name="{{ $field }}" value="0" />
                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $settings->$field)) class="mt-1 h-4 w-4 shrink-0 rounded border-zinc-300 bg-white text-red-600 focus:ring-red-500" />
                    <span>
                        <span class="block font-medium text-zinc-900">{{ $label }}</span>
                        <span class="mt-0.5 block text-sm text-zinc-600">{{ $help }}</span>
                    </span>
                </label>
            @endforeach

            <div class="pt-4">
                <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Save modules</button>
            </div>
        </form>
    </div>
@endsection
