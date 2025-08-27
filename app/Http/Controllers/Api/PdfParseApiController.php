<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\ApiKey;
use App\Models\PdfParse;
use App\Services\PdfParseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PdfParseApiController extends Controller
{
    public function __construct(
        private PdfParseService $pdfParseService
    ) {}

    public function parse(Request $request): JsonResponse
    {
        // Validate API key
        $apiKey = $this->validateApiKey($request);
        if (!$apiKey) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $user = $apiKey->user;
        
        // Check if user has active subscription
        if (!$user->hasActiveSubscription()) {
            return response()->json(['error' => 'Active subscription required'], 403);
        }

        // Validate request
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'ai_model' => 'nullable|string', // 'openai' or 'gemini'
        ]);

        // Get AI model
        $aiModel = null;
        if ($request->has('ai_model')) {
            $aiModel = AiModel::where('name', strtolower($request->ai_model))->active()->first();
            if (!$aiModel) {
                return response()->json(['error' => 'Invalid AI model specified'], 400);
            }
        } else {
            // Use user's preferred model or default
            $aiModel = AiModel::where('name', $user->preferred_ai_model)->active()->first();
            if (!$aiModel) {
                $aiModel = AiModel::active()->first();
            }
        }

        if (!$aiModel) {
            return response()->json(['error' => 'No AI model available'], 500);
        }

        try {
            // Parse PDF
            $pdfParse = $this->pdfParseService->parsePdf($user, $request->file('pdf_file'), $aiModel);
            
            // Mark API key as used
            $apiKey->markAsUsed();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pdfParse->id,
                    'status' => $pdfParse->status,
                    'result' => $pdfParse->parse_result,
                    'ai_model' => $aiModel->name,
                    'processed_at' => $pdfParse->processed_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to parse PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function status(Request $request, $id): JsonResponse
    {
        $apiKey = $this->validateApiKey($request);
        if (!$apiKey) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $user = $apiKey->user;
        $pdfParse = PdfParse::where('id', $id)->where('user_id', $user->id)->first();

        if (!$pdfParse) {
            return response()->json(['error' => 'PDF parse not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pdfParse->id,
                'status' => $pdfParse->status,
                'result' => $pdfParse->parse_result,
                'error' => $pdfParse->error_message,
                'processed_at' => $pdfParse->processed_at,
                'created_at' => $pdfParse->created_at,
            ]
        ]);
    }

    private function validateApiKey(Request $request): ?ApiKey
    {
        $apiKey = $request->header('X-API-Key');
        $apiSecret = $request->header('X-API-Secret');

        if (!$apiKey || !$apiSecret) {
            return null;
        }

        return ApiKey::where('key', $apiKey)
            ->where('secret', $apiSecret)
            ->where('is_active', true)
            ->first();
    }
}
