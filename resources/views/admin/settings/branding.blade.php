@extends('admin.layout')

@section('title', 'Branding')

@php
    $inp = 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
    $lbl = 'mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500';
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Branding & appearance',
        'description' => 'Name, tagline, logo, and theme colors. The mobile app reads these from GET /api/v1/bootstrap after launch.',
    ])

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <form method="post" action="{{ route('admin.settings.branding.update') }}" class="grid max-w-3xl gap-6 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">App name</label>
                <input name="app_name" value="{{ old('app_name', $settings->app_name) }}" required class="{{ $inp }}" />
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">Tagline</label>
                <input name="app_tagline" value="{{ old('app_tagline', $settings->app_tagline) }}" placeholder="Short subtitle under the name" class="{{ $inp }}" />
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">Logo URL</label>
                <input name="logo_url" type="url" value="{{ old('logo_url', $settings->logo_url) }}" placeholder="https://…" class="{{ $inp }}" />
            </div>
            <div>
                <label class="{{ $lbl }}">Primary color</label>
                <div class="flex gap-2">
                    <input name="primary_color_hex" value="{{ old('primary_color_hex', $settings->primary_color_hex) }}" required pattern="^#[0-9A-Fa-f]{6}$" class="{{ $inp }} font-mono" />
                    <input type="color" value="{{ old('primary_color_hex', $settings->primary_color_hex) }}" class="h-11 w-14 cursor-pointer rounded-xl border border-zinc-200 bg-white p-1" aria-label="Pick primary color"
                        oninput="this.previousElementSibling.value=this.value" />
                </div>
            </div>
            <div>
                <label class="{{ $lbl }}">Secondary color</label>
                @php
                    $secondaryHex = old('secondary_color_hex', $settings->secondary_color_hex);
                    $secondaryPicker = $secondaryHex && preg_match('/^#[0-9A-Fa-f]{6}$/i', $secondaryHex) ? $secondaryHex : '#64748b';
                @endphp
                <div class="flex gap-2">
                    <input name="secondary_color_hex" value="{{ $secondaryHex }}" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#optional" class="{{ $inp }} font-mono" />
                    <input type="color" value="{{ $secondaryPicker }}" class="h-11 w-14 cursor-pointer rounded-xl border border-zinc-200 bg-white p-1" aria-label="Pick secondary color"
                        oninput="this.previousElementSibling.value=this.value" />
                </div>
            </div>

            <div class="sm:col-span-2 flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Save branding</button>
            </div>
        </form>
    </div>
@endsection
