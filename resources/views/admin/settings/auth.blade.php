@extends('admin.layout')

@section('title', 'Phone sign-in')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Phone sign-in',
        'description' => 'Rules the mobile client should enforce before Firebase phone OTP. Actual SMS is still configured in the Firebase console.',
    ])

    @include('admin.settings.partials.auth_form', ['settings' => $settings])
@endsection
