<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\AiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(): Response
    {
        $users = User::with(['activeSubscription.subscription'])
            ->withCount(['pdfParses', 'apiKeys'])
            ->latest()
            ->paginate(20);
        
        $availableSubscriptions = Subscription::active()->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'availableSubscriptions' => $availableSubscriptions,
        ]);
    }

    public function show(User $user): Response
    {
        $user->load(['userSubscriptions.subscription', 'apiKeys', 'pdfParses.aiModel']);

        return Inertia::render('Admin/UserManagement/Show', [
            'user' => $user,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:super,user',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function assignSubscription(Request $request, User $user)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'duration_months' => 'required|integer|min:1|max:12',
        ]);

        // Cancel existing active subscription
        $user->userSubscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        // Create new subscription
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $request->subscription_id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonths($request->duration_months),
        ]);

        return back()->with('success', 'Subscription assigned successfully.');
    }

    public function revokeSubscription(User $user)
    {
        $user->userSubscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return back()->with('success', 'Subscription revoked successfully.');
    }
}
