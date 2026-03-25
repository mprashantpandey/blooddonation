@extends('admin.layout')

@section('title', 'Cities')

@php
    $inp = 'w-full rounded-xl border border-zinc-200/80 bg-white/80 px-3 py-2.5 text-sm text-zinc-900 shadow-inner placeholder:text-zinc-500 focus:border-red-500/80 focus:outline-none focus:ring-2 focus:ring-red-500/20';
    $lbl = 'mb-1.5 block text-xs font-medium uppercase tracking-wide text-zinc-500';
@endphp

@section('content')
    @include('admin.partials.settings-intro', [
        'title' => 'Cities',
        'description' => 'Manage active/inactive cities used by donor matching and blood requests.',
    ])

    <div class="mb-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
        <h2 class="mb-4 text-sm font-semibold text-zinc-900">Add city</h2>
        <form method="post" action="{{ route('admin.cities.store') }}" class="grid gap-4 sm:grid-cols-3">
            @csrf
            <div class="sm:col-span-2">
                <label class="{{ $lbl }}">City name</label>
                <input name="city_name" required class="{{ $inp }}" placeholder="e.g. Pune" />
            </div>
            <div>
                <label class="{{ $lbl }}">Status</label>
                <select name="status" class="{{ $inp }}">
                    <option value="active">active</option>
                    <option value="inactive">inactive</option>
                </select>
            </div>
            <div class="sm:col-span-3">
                <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-500">Add city</button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-4 shadow-xl sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm datatable">
                <thead class="text-left text-zinc-600">
                    <tr class="border-b border-zinc-200">
                        <th class="pb-3 pr-4">Name</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/80">
                    @forelse ($cities as $city)
                        <tr>
                            <td class="py-3 pr-4">
                                <form method="post" action="{{ route('admin.cities.update', $city) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    @csrf
                                    @method('PUT')
                                    <input name="city_name" value="{{ $city->city_name }}" class="{{ $inp }} sm:w-64" />
                            </td>
                            <td class="py-3 pr-4">
                                    <select name="status" class="{{ $inp }} sm:w-36">
                                        <option value="active" @selected($city->status === 'active')>active</option>
                                        <option value="inactive" @selected($city->status === 'inactive')>inactive</option>
                                    </select>
                            </td>
                            <td class="py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button class="rounded-lg border border-zinc-300 px-3 py-2 text-xs font-semibold text-zinc-800 hover:border-zinc-400">Save</button>
                                </form>
                                        <form method="post" action="{{ route('admin.cities.destroy', $city) }}" onsubmit="return confirm('Delete this city?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-300 px-3 py-2 text-xs font-semibold text-red-700 hover:border-red-400">Delete</button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-zinc-500">No cities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $cities->links() }}</div>
    </div>
@endsection

