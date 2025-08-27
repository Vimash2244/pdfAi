<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\PdfParse;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Get user's API keys with usage statistics
        $apiKeys = $user->apiKeys()
            ->select('id', 'name', 'key', 'is_active', 'last_used_at', 'created_at')
            ->latest()
            ->get()
            ->map(function ($apiKey) {
                return [
                    'id' => $apiKey->id,
                    'name' => $apiKey->name,
                    'key' => $apiKey->key,
                    'masked_key' => substr($apiKey->key, 0, 8) . '...' . substr($apiKey->key, -4),
                    'is_active' => $apiKey->is_active,
                    'last_used_at' => $apiKey->last_used_at?->diffForHumans(),
                    'created_at' => $apiKey->created_at->format('M j, Y'),
                    'status' => $apiKey->is_active ? 'Active' : 'Inactive',
                ];
            });

        // Get PDF parse statistics
        $pdfStats = [
            'total_parses' => $user->pdfParses()->count(),
            'completed_parses' => $user->pdfParses()->completed()->count(),
            'failed_parses' => $user->pdfParses()->failed()->count(),
            'recent_parses' => $user->pdfParses()
                ->with('aiModel')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($parse) {
                    return [
                        'id' => $parse->id,
                        'filename' => $parse->original_filename,
                        'status' => $parse->status,
                        'ai_model' => $parse->aiModel?->name ?? 'Unknown',
                        'processed_at' => $parse->processed_at?->diffForHumans(),
                        'file_size' => $this->formatFileSize($parse->file_size_bytes),
                    ];
                }),
        ];

        // Get subscription information
        $subscription = $user->activeSubscription;
        $subscriptionInfo = null;
        
        if ($subscription) {
            $subscriptionInfo = [
                'name' => $subscription->subscription->name,
                'status' => $subscription->status,
                'expires_at' => $subscription->expires_at->format('M j, Y'),
                'days_remaining' => $subscription->expires_at->diffInDays(now()),
                'features' => $subscription->subscription->features ?? [],
                'can_use_ai' => $user->canUseAiModel(),
            ];
        }

        // Get available AI models
        $aiModels = AiModel::active()->get(['id', 'name', 'model_identifier', 'description']);

        // Get quick stats
        $quickStats = [
            'api_keys_count' => $apiKeys->count(),
            'active_api_keys' => $apiKeys->where('is_active', true)->count(),
            'total_pdf_parses' => $pdfStats['total_parses'],
            'success_rate' => $pdfStats['total_parses'] > 0 
                ? round(($pdfStats['completed_parses'] / $pdfStats['total_parses']) * 100, 1)
                : 0,
        ];

        return Inertia::render('Dashboard', [
            'apiKeys' => $apiKeys,
            'pdfStats' => $pdfStats,
            'subscription' => $subscriptionInfo,
            'aiModels' => $aiModels,
            'quickStats' => $quickStats,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
            ],
        ]);
    }

    private function formatFileSize($bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
