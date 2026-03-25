@extends('admin.layout')

@section('title', 'Rewards')

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Points & rewards',
        'description' => 'Configure default points, referral bonuses, donor verification rule, and badge thresholds. These values are used by the API and admin approvals.',
    ])

    @include('admin.settings.partials.rewards_form', ['settings' => $settings])
@endsection

