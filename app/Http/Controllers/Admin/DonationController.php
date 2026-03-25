<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\WalletEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $donations = Donation::query()
            ->with(['donor.user', 'bloodRequest'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.donations.index', [
            'donations' => $donations,
            'statusFilter' => $status,
        ]);
    }

    public function approve(Request $request, Donation $donation): RedirectResponse
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);

        DB::transaction(function () use ($donation, $validated) {
            $donation->refresh();
            $wasApproved = $donation->status === 'approved';
            $previousPoints = (int) $donation->points;

            $donation->status = 'approved';
            $donation->points = $validated['points'];
            $donation->save();

            if (! $wasApproved && $validated['points'] > 0) {
                $donorUserId = $donation->donor?->user_id;
                if ($donorUserId !== null) {
                    WalletEntry::query()->create([
                        'user_id' => $donorUserId,
                        'points' => $validated['points'],
                        'type' => 'donation',
                        'description' => "Donation #{$donation->id} approved",
                    ]);
                }
            } elseif ($wasApproved && $validated['points'] !== $previousPoints) {
                $delta = $validated['points'] - $previousPoints;
                $donorUserId = $donation->donor?->user_id;
                if ($delta !== 0 && $donorUserId !== null) {
                    WalletEntry::query()->create([
                        'user_id' => $donorUserId,
                        'points' => $delta,
                        'type' => 'donation',
                        'description' => "Donation #{$donation->id} points adjusted",
                    ]);
                }
            }
        });

        return back()->with('status', 'Donation approved and wallet updated.');
    }

    public function reject(Donation $donation): RedirectResponse
    {
        $donation->status = 'rejected';
        $donation->points = 0;
        $donation->save();

        return back()->with('status', 'Donation rejected.');
    }
}

