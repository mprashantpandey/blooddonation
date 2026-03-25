@extends('admin.layout')

@section('title', 'Settings')

@php
    $tab = $tab ?? 'branding';
    $tabs = [
        'branding' => ['Branding', 'Branding & appearance'],
        'welcome' => ['Welcome', 'Welcome dialog'],
        'features' => ['Modules', 'Modules & features'],
        'rewards' => ['Rewards', 'Points & rewards'],
        'auth' => ['Auth', 'Phone sign-in'],
        'firebase' => ['Firebase', 'Firebase & FCM'],
        'cron' => ['Cron', 'Cron / queue setup'],
    ];
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Settings',
        'description' => $tabs[$tab][1] ?? 'Configure the mobile app and server features.',
    ])

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach ($tabs as $key => [$label])
            <a
                href="{{ route('admin.settings', ['tab' => $key]) }}"
                class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $tab === $key ? 'bg-red-600 text-white shadow-lg shadow-red-900/25' : 'border border-zinc-200 bg-white/60 text-zinc-700 hover:bg-zinc-50' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($tab === 'branding')
        @include('admin.settings.partials.branding_form', ['settings' => $settings])
    @elseif ($tab === 'welcome')
        @include('admin.settings.partials.welcome_form', ['settings' => $settings])
    @elseif ($tab === 'features')
        @include('admin.settings.partials.features_form', ['settings' => $settings])
    @elseif ($tab === 'rewards')
        @include('admin.settings.partials.rewards_form', ['settings' => $settings])
    @elseif ($tab === 'auth')
        @include('admin.settings.partials.auth_form', ['settings' => $settings])
    @elseif ($tab === 'firebase')
        @include('admin.settings.partials.firebase_form', [
            'settings' => $settings,
            'firebase_preview' => $firebase_preview ?? '',
            'fcm_service_preview' => $fcm_service_preview ?? '',
            'firebase_web_preview' => $firebase_web_preview ?? '',
        ])
    @elseif ($tab === 'cron')
        @include('admin.settings.partials.cron_tab')
    @else
        @include('admin.settings.partials.branding_form', ['settings' => $settings])
    @endif
@endsection

