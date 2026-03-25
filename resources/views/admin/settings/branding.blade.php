@extends('admin.layout')

@section('title', 'Branding')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Branding & appearance',
        'description' => 'Name, tagline, logo, and theme colors. The mobile app reads these from GET /api/v1/bootstrap after launch.',
    ])

    @include('admin.settings.partials.branding_form', ['settings' => $settings])
@endsection
