@extends('admin.layout')

@section('title', 'Firebase')

@php
    $inp = 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner font-mono text-xs leading-relaxed focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Firebase & FCM',
        'description' => 'Three separate pastes: mobile client options (Flutter), server service account (Laravel FCM HTTP v1), and web app config (bootstrap API for web/PWA). Server and mobile JSON are encrypted at rest; web config is also stored encrypted but is safe to expose via the public bootstrap endpoint.',
    ])

    <div class="mb-8 grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-amber-200/70 bg-amber-50/80 p-4 text-sm text-amber-900/90">
            <strong class="text-amber-800">Priority</strong>
            <p class="mt-2 leading-relaxed text-amber-800/80">If <code class="rounded bg-black/30 px-1">FIREBASE_SERVICE_ACCOUNT_PATH</code> (or JSON / base64) is set in <code class="rounded bg-black/30 px-1">.env</code>, it overrides the admin service account for FCM.</p>
        </div>
        <div class="rounded-2xl border border-red-200/70 bg-red-50/80 p-4 text-sm text-red-800/90">
            <strong class="text-red-800">Server JSON</strong>
            <p class="mt-2 leading-relaxed text-red-800/80">Use the <strong>private key</strong> file from Firebase Console → Project settings → Service accounts → Generate new private key. Never expose this in the mobile app or <code class="rounded bg-black/30 px-1">/api/v1/bootstrap</code>.</p>
        </div>
        <div class="rounded-2xl border border-zinc-200/80 bg-white/60 p-4 text-sm text-zinc-600">
            <strong class="text-zinc-700">Web credentials</strong>
            <p class="mt-2 leading-relaxed">Paste the Firebase <strong>web app</strong> config object (from Add app → Web). It is published under <code class="text-red-600">firebase_web</code> in the bootstrap JSON for public clients.</p>
        </div>
    </div>

    <div class="mb-8 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <h2 class="mb-2 text-lg font-semibold text-zinc-900">FCM server (service account JSON)</h2>
        <p class="mb-6 max-w-3xl text-sm text-zinc-500">Paste the full JSON with <code class="text-red-300">private_key</code> and <code class="text-red-300">client_email</code>. Laravel uses it for <code class="text-red-300">FCM HTTP v1</code> when env credentials are not set.</p>
        <form method="post" action="{{ route('admin.settings.firebase.fcm.update') }}" class="max-w-4xl space-y-4">
            @csrf
            @method('PUT')
            <textarea name="fcm_service_account_json" rows="16" placeholder='{"type":"service_account","project_id":"…","private_key_id":"…","private_key":"…","client_email":"…@….iam.gserviceaccount.com",...}' class="{{ $inp }}">{{ old('fcm_service_account_json', $fcm_service_preview) }}</textarea>
            @error('fcm_service_account_json')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Save service account</button>
        </form>
    </div>

    <div class="mb-8 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <h2 class="mb-2 text-lg font-semibold text-zinc-900">Web app credentials (bootstrap)</h2>
        <p class="mb-6 max-w-3xl text-sm text-zinc-500">Typical keys: <code class="text-red-300">apiKey</code>, <code class="text-red-300">authDomain</code>, <code class="text-red-300">projectId</code>, <code class="text-red-300">storageBucket</code>, <code class="text-red-300">messagingSenderId</code>, <code class="text-red-300">appId</code>. Shipped as <code class="text-red-300">firebase_web</code> in <code class="text-red-300">GET /api/v1/bootstrap</code>.</p>
        <form method="post" action="{{ route('admin.settings.firebase.web.update') }}" class="max-w-4xl space-y-4">
            @csrf
            @method('PUT')
            <textarea name="firebase_web_credentials_json" rows="10" placeholder='{"apiKey":"…","authDomain":"….firebaseapp.com","projectId":"…","storageBucket":"….appspot.com","messagingSenderId":"…","appId":"1:…:web:…"}' class="{{ $inp }}">{{ old('firebase_web_credentials_json', $firebase_web_preview) }}</textarea>
            @error('firebase_web_credentials_json')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Save web credentials</button>
        </form>
    </div>

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <h2 class="mb-2 text-lg font-semibold text-zinc-900">Mobile app (Flutter options JSON)</h2>
        <p class="mb-6 max-w-3xl text-sm text-zinc-500">Optional remote fallback for native apps. Prefer <code class="text-red-300">flutterfire configure</code>. Merged into <code class="text-red-300">firebase</code> in bootstrap.</p>
        <form method="post" action="{{ route('admin.settings.firebase.update') }}" class="max-w-4xl space-y-4">
            @csrf
            @method('PUT')
            <textarea name="firebase_options_json" rows="12" placeholder='{"apiKey":"…","appId":"…","messagingSenderId":"…","projectId":"…","storageBucket":"…"}' class="{{ $inp }}">{{ old('firebase_options_json', $firebase_preview) }}</textarea>
            @error('firebase_options_json')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            <button type="submit" class="rounded-xl border border-zinc-300 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-900 transition hover:bg-zinc-50">Save mobile JSON</button>
        </form>
    </div>
@endsection
