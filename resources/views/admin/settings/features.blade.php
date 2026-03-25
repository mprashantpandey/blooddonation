@extends('admin.layout')

@section('title', 'Modules')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Modules & features',
        'description' => 'Turn sections of the mobile app on or off. The app reads these flags from the bootstrap API and can hide navigation or screens accordingly.',
    ])

    @include('admin.settings.partials.features_form', ['settings' => $settings])
@endsection
