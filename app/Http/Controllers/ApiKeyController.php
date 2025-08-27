<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ApiKeyController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $apiKeys = $user->apiKeys()->latest()->get();

        return Inertia::render('ApiKeys/Index', [
            'apiKeys' => $apiKeys,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        
        ApiKey::create([
            'user_id' => $user->id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'API key created successfully.');
    }

    public function toggle(ApiKey $apiKey)
    {
        $user = Auth::user();
        if ($apiKey->user_id !== $user->id) {
            abort(403);
        }

        $apiKey->update(['is_active' => !$apiKey->is_active]);

        return back()->with('success', 'API key status updated successfully.');
    }

    public function destroy(ApiKey $apiKey)
    {
        $user = Auth::user();
        if ($apiKey->user_id !== $user->id) {
            abort(403);
        }

        $apiKey->delete();

        return back()->with('success', 'API key deleted successfully.');
    }
}
