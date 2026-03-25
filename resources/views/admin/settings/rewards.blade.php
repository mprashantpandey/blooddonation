@extends('admin.layout')

@section('title', 'Rewards')

@php
    $inp = 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
    $lbl = 'mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500';
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Points & rewards',
        'description' => 'Configure default points, referral bonuses, donor verification rule, and badge thresholds. These values are used by the API and admin approvals.',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <form method="post" action="{{ route('admin.settings.rewards.update') }}" class="grid max-w-4xl gap-6 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="sm:col-span-2">
                <div class="text-sm font-semibold text-zinc-900">Points</div>
                <p class="mt-1 text-sm text-zinc-600">Referral points are awarded on signup when a valid referral code is used. Donation points are suggested as the default when approving donation proofs.</p>
            </div>

            <div>
                <label class="{{ $lbl }}">Donation default points</label>
                <input name="points_donation_default" type="number" min="0" max="10000" value="{{ old('points_donation_default', $settings->points_donation_default) }}" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Referral points (referrer)</label>
                <input name="points_referral_referrer" type="number" min="0" max="10000" value="{{ old('points_referral_referrer', $settings->points_referral_referrer) }}" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Referral points (new user)</label>
                <input name="points_referral_new_user" type="number" min="0" max="10000" value="{{ old('points_referral_new_user', $settings->points_referral_new_user) }}" class="{{ $inp }}" />
            </div>

            <div class="sm:col-span-2 mt-2">
                <div class="text-sm font-semibold text-zinc-900">Verification</div>
                <p class="mt-1 text-sm text-zinc-600">When a donor has at least N approved donations, their donor profile becomes verified and the verified badge is assigned.</p>
            </div>
            <div>
                <label class="{{ $lbl }}">Approved donations required</label>
                <input name="verified_after_approved_donations" type="number" min="1" max="50" value="{{ old('verified_after_approved_donations', $settings->verified_after_approved_donations) }}" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Verified badge name</label>
                <input name="badge_verified_name" value="{{ old('badge_verified_name', $settings->badge_verified_name) }}" class="{{ $inp }}" />
            </div>

            <div class="sm:col-span-2 mt-2">
                <div class="text-sm font-semibold text-zinc-900">Badges</div>
                <p class="mt-1 text-sm text-zinc-600">Badges are assigned automatically when thresholds are reached (approved donations / referrals).</p>
            </div>

            <div>
                <label class="{{ $lbl }}">Donation badge 1 threshold</label>
                <input name="badge_donation_1_threshold" type="number" min="1" max="50" value="{{ old('badge_donation_1_threshold', $settings->badge_donation_1_threshold) }}" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Donation badge 1 name</label>
                <input name="badge_donation_1_name" value="{{ old('badge_donation_1_name', $settings->badge_donation_1_name) }}" class="{{ $inp }}" />
            </div>

            <div>
                <label class="{{ $lbl }}">Donation badge 3 threshold</label>
                <input name="badge_donation_3_threshold" type="number" min="1" max="50" value="{{ old('badge_donation_3_threshold', $settings->badge_donation_3_threshold) }}" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Donation badge 3 name</label>
                <input name="badge_donation_3_name" value="{{ old('badge_donation_3_name', $settings->badge_donation_3_name) }}" class="{{ $inp }}" />
            </div>

            <div>
                <label class="{{ $lbl }}">Donation badge 5 threshold</label>
                <input name="badge_donation_5_threshold" type="number" min="1" max="50" value="{{ old('badge_donation_5_threshold', $settings->badge_donation_5_threshold) }}" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Donation badge 5 name</label>
                <input name="badge_donation_5_name" value="{{ old('badge_donation_5_name', $settings->badge_donation_5_name) }}" class="{{ $inp }}" />
            </div>

            <div>
                <label class="{{ $lbl }}">Referral badge threshold</label>
                <input name="badge_referral_threshold" type="number" min="1" max="200" value="{{ old('badge_referral_threshold', $settings->badge_referral_threshold) }}" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Referral badge name</label>
                <input name="badge_referral_name" value="{{ old('badge_referral_name', $settings->badge_referral_name) }}" class="{{ $inp }}" />
            </div>

            <div class="sm:col-span-2 flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Save rewards</button>
            </div>
        </form>
    </div>
@endsection

