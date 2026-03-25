@extends('admin.layout')

@section('title', 'Welcome dialog')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Welcome dialog',
        'description' => 'Shown on the mobile home screen when enabled. You can require “show once” so returning users are not interrupted.',
    ])

    @include('admin.settings.partials.welcome_form', ['settings' => $settings])
@endsection
