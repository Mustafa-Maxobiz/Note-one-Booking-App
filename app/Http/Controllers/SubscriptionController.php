<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of user's subscriptions.
     */
    public function index()
    {
        $subscriptions = Auth::user()->subscriptions()->with('package')->orderBy('id', 'desc')->get();
        
        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create()
    {
        $packages = Package::where('is_active', true)->get();
        
        return view('subscriptions.create', compact('packages'));
    }

    /**
     * Store a newly created subscription.
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $package = Package::findOrFail($request->package_id);
        
        // Check if user already has an active subscription
        if (Auth::user()->hasActiveSubscription()) {
            return redirect()->back()->with('error', 'You already have an active subscription.');
        }

        // Create subscription (for now, we'll simulate Stripe integration)
        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonths($package->duration_months),
        ]);

        // Update user's subscription status
        Auth::user()->update(['subscription_status' => 'active']);

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription created successfully! You now have access for ' . $package->duration_text);
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel the specified subscription.
     */
    public function cancel(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Update user's subscription status
        Auth::user()->update(['subscription_status' => 'expired']);

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription cancelled successfully.');
    }
}
