<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CitySlider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitySliderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ]);

        $sliders = CitySlider::query()
            ->where('city_id', $data['city_id'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (CitySlider $s) => [
                'id' => $s->id,
                'city_id' => $s->city_id,
                'title' => $s->title,
                'image_url' => $s->image_url,
                'sort_order' => $s->sort_order,
            ]);

        return response()->json(['data' => $sliders]);
    }
}

