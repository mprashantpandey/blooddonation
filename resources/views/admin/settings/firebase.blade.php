@extends('admin.layout')

@section('title', 'Firebase')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Firebase & FCM',
        'description' => 'Three separate pastes: mobile client options (Flutter), server service account (Laravel FCM HTTP v1), and web app config (bootstrap API for web/PWA). Server and mobile JSON are encrypted at rest; web config is also stored encrypted but is safe to expose via the public bootstrap endpoint.',
    ])

    @include('admin.settings.partials.firebase_form', [
        'settings' => $settings,
        'firebase_preview' => $firebase_preview ?? '',
        'fcm_service_preview' => $fcm_service_preview ?? '',
        'firebase_web_preview' => $firebase_web_preview ?? '',
    ])
@endsection
