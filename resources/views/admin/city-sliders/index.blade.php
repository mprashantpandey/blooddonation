@extends('admin.layout')

@section('title', 'City Sliders')

@php
    $inp = 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
    $lbl = 'mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500';
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'City-wise image sliders',
        'description' => 'Upload banner images per city. Mobile app can fetch and show these as a carousel.',
    ])

    <div class="mb-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <form method="post" action="{{ route('admin.city-sliders.store') }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label class="{{ $lbl }}">City</label>
                <select name="city_id" required class="{{ $inp }}">
                    <option value="">Select city</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $lbl }}">Sort order</label>
                <input name="sort_order" type="number" min="0" value="0" class="{{ $inp }}" />
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">Title (optional)</label>
                <input name="title" class="{{ $inp }}" placeholder="e.g. Donate today, save lives" />
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">Image</label>
                <input name="image" type="file" accept="image/*" required class="{{ $inp }}" />
            </div>
            <div class="sm:col-span-2">
                <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-zinc-300 bg-white text-red-600 focus:ring-red-500" />
                    Active
                </label>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-red-500">Upload slider</button>
            </div>
        </form>
    </div>

    <div class="mb-4 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4">
        <form method="get" class="flex items-center gap-3">
            <select name="city_id" class="{{ $inp }} max-w-xs">
                <option value="">All cities</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected($cityId === $city->id)>{{ $city->city_name }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-white border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-800">Filter</button>
        </form>
    </div>

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 shadow-xl sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm datatable">
                <thead class="text-left text-zinc-600">
                    <tr class="border-b border-zinc-200">
                        <th class="pb-3 pr-4">Preview</th>
                        <th class="pb-3 pr-4">City</th>
                        <th class="pb-3 pr-4">Title</th>
                        <th class="pb-3 pr-4">Order</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/80">
                    @forelse ($sliders as $slider)
                        <tr>
                            <td class="py-3 pr-4">
                                <img src="{{ $slider->image_url }}" alt="slider" class="h-14 w-24 rounded-lg border border-zinc-200 object-cover" />
                            </td>
                            <td class="py-3 pr-4 text-zinc-700">{{ $slider->city?->city_name ?? '—' }}</td>
                            <td class="py-3 pr-4 text-zinc-700">{{ $slider->title ?: '—' }}</td>
                            <td class="py-3 pr-4 text-zinc-700">{{ $slider->sort_order }}</td>
                            <td class="py-3">
                                <form method="post" action="{{ route('admin.city-sliders.update', $slider) }}" enctype="multipart/form-data" class="mb-2 grid gap-2 md:grid-cols-5">
                                    @csrf
                                    @method('PUT')
                                    <select name="city_id" class="rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900">
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @selected($slider->city_id === $city->id)>{{ $city->city_name }}</option>
                                        @endforeach
                                    </select>
                                    <input name="title" value="{{ $slider->title }}" placeholder="Title" class="rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900" />
                                    <input name="sort_order" type="number" min="0" value="{{ $slider->sort_order }}" class="rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900" />
                                    <input name="image" type="file" accept="image/*" class="rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900" />
                                    <label class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs text-zinc-900">
                                        <input type="checkbox" name="is_active" value="1" @checked($slider->is_active) class="h-3.5 w-3.5 rounded border-zinc-300 text-red-600" />
                                        Active
                                    </label>
                                    <button class="rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-800 hover:border-zinc-400">Save</button>
                                </form>
                                <form method="post" action="{{ route('admin.city-sliders.destroy', $slider) }}" onsubmit="return confirm('Delete this slider?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 hover:border-red-400">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-zinc-500">No slider images yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $sliders->links() }}</div>
    </div>
@endsection

