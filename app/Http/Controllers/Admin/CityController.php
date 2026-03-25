<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(): View
    {
        return view('admin.cities.index', [
            'cities' => City::query()->orderBy('city_name')->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        City::query()->create($validated);

        return redirect()->route('admin.cities.index')->with('status', 'City added.');
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'city_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $city->update($validated);

        return redirect()->route('admin.cities.index')->with('status', 'City updated.');
    }

    public function destroy(City $city): RedirectResponse
    {
        $hasReferences = $city->users()->exists() || $city->bloodRequests()->exists();
        if ($hasReferences) {
            return redirect()->route('admin.cities.index')->withErrors([
                'city_name' => 'Cannot delete city with linked users or requests.',
            ]);
        }

        $city->delete();

        return redirect()->route('admin.cities.index')->with('status', 'City deleted.');
    }
}

