@php
    $inp = $inp ?? 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
    $lbl = $lbl ?? 'mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500';
@endphp

<div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
    <form method="post" action="{{ route('admin.settings.welcome.update') }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-4 rounded-xl border border-zinc-200/60 bg-white/60 p-4 sm:flex-row sm:flex-wrap">
            <label class="flex cursor-pointer items-center gap-3 text-sm text-zinc-700">
                <input type="hidden" name="welcome_enabled" value="0" />
                <input type="checkbox" name="welcome_enabled" value="1" @checked(old('welcome_enabled', $settings->welcome_enabled)) class="h-4 w-4 rounded border-zinc-300 bg-white text-red-600 focus:ring-red-500" />
                Enable welcome dialog
            </label>
            <label class="flex cursor-pointer items-center gap-3 text-sm text-zinc-700">
                <input type="hidden" name="welcome_show_once" value="0" />
                <input type="checkbox" name="welcome_show_once" value="1" @checked(old('welcome_show_once', $settings->welcome_show_once)) class="h-4 w-4 rounded border-zinc-300 bg-white text-red-600 focus:ring-red-500" />
                Remember dismissal (one time per app update)
            </label>
        </div>

        <div>
            <label class="{{ $lbl }}">Title</label>
            <input name="welcome_title" value="{{ old('welcome_title', $settings->welcome_title) }}" class="{{ $inp }}" />
        </div>
        <div>
            <label class="{{ $lbl }}">Message</label>
            <textarea name="welcome_message" rows="5" class="{{ $inp }}">{{ old('welcome_message', $settings->welcome_message) }}</textarea>
        </div>
        <div class="max-w-xs">
            <label class="{{ $lbl }}">Button label</label>
            <input name="welcome_button_text" value="{{ old('welcome_button_text', $settings->welcome_button_text) }}" class="{{ $inp }}" />
        </div>

        <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Save welcome</button>
    </form>
</div>

