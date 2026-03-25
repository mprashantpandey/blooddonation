<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BloodRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');

        $requests = BloodRequest::query()
            ->with(['city', 'user'])
            ->withCount([
                'requestResponses as interested_count' => function ($q) {
                    $q->where('status', 'interested');
                },
                'requestResponses as ignored_count' => function ($q) {
                    $q->where('status', 'ignored');
                },
            ])
            ->when(in_array($status, ['open', 'closed', 'fulfilled'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.requests.index', [
            'requests' => $requests,
            'statusFilter' => $status,
        ]);
    }

    public function show(BloodRequest $bloodRequest): View
    {
        $bloodRequest->load([
            'city',
            'user',
            'requestResponses.donor.user.city',
            'requestResponses.donor.user.badges',
        ]);

        $responses = $bloodRequest->requestResponses
            ->sortByDesc('created_at')
            ->values();

        $interested = $responses->where('status', 'interested')->values();
        $ignored = $responses->where('status', 'ignored')->values();

        return view('admin.requests.show', [
            'requestModel' => $bloodRequest,
            'responses' => $responses,
            'interested' => $interested,
            'ignored' => $ignored,
        ]);
    }

    public function updateStatus(Request $request, BloodRequest $bloodRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,closed,fulfilled'],
        ]);

        $bloodRequest->status = $validated['status'];
        $bloodRequest->save();

        return back()->with('status', 'Request status updated.');
    }
}

