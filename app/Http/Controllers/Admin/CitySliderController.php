<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CitySlider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CitySliderController extends Controller
{
    public function index(Request $request): View
    {
        $cityId = $request->integer('city_id');

        $sliders = CitySlider::query()
            ->with('city')
            ->when($cityId > 0, fn ($q) => $q->where('city_id', $cityId))
            ->orderBy('city_id')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.city-sliders.index', [
            'sliders' => $sliders,
            'cities' => City::query()->orderBy('city_name')->get(),
            'cityId' => $cityId > 0 ? $cityId : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('city-sliders', 'public');

        CitySlider::query()->create([
            'city_id' => $validated['city_id'],
            'title' => $validated['title'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $path,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.city-sliders.index')->with('status', 'Slider image uploaded.');
    }

    public function update(Request $request, CitySlider $citySlider): RedirectResponse
    {
        $validated = $request->validate([
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $citySlider->city_id = $validated['city_id'];
        $citySlider->title = $validated['title'] ?? null;
        $citySlider->sort_order = $validated['sort_order'];
        $citySlider->is_active = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($citySlider->image_path !== '') {
                Storage::disk('public')->delete($citySlider->image_path);
            }
            $citySlider->image_path = $request->file('image')->store('city-sliders', 'public');
        }

        $citySlider->save();

        return back()->with('status', 'Slider updated.');
    }

    public function destroy(CitySlider $citySlider): RedirectResponse
    {
        if ($citySlider->image_path !== '') {
            Storage::disk('public')->delete($citySlider->image_path);
        }
        $citySlider->delete();

        return back()->with('status', 'Slider removed.');
    }
}

