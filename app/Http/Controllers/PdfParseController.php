<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\PdfParse;
use App\Services\PdfParseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PdfParseController extends Controller
{
    public function __construct(
        private PdfParseService $pdfParseService
    ) {}

    public function index(): Response
    {
        $user = Auth::user();
        $aiModels = AiModel::active()->get();
        $pdfParses = $user->pdfParses()->with('aiModel')->latest()->paginate(10);

        return Inertia::render('PdfParse/Index', [
            'aiModels' => $aiModels,
            'pdfParses' => $pdfParses,
            'canUseAi' => $user->canUseAiModel(),
        ]);
    }

    public function history(): Response
    {
        $user = Auth::user();
        $pdfParses = $user->pdfParses()->with('aiModel')->latest()->paginate(20);

        return Inertia::render('PdfParse/History', [
            'pdfParses' => $pdfParses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'ai_model_id' => 'required|exists:ai_models,id',
        ]);

        $user = Auth::user();
        
        if (!$user->canUseAiModel()) {
            return back()->withErrors(['message' => 'You need an active subscription to use AI models.']);
        }

        $aiModel = AiModel::findOrFail($request->ai_model_id);
        
        try {
            $pdfParse = $this->pdfParseService->parsePdf($user, $request->file('pdf_file'), $aiModel);
            
            return back()->with('success', 'PDF parsed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Failed to parse PDF: ' . $e->getMessage()]);
        }
    }

    public function show(PdfParse $pdfParse): Response
    {
        $user = Auth::user();
        if ($pdfParse->user_id !== $user->id && !$user->isSuper()) {
            abort(403);
        }

        return Inertia::render('PdfParse/Show', [
            'pdfParse' => $pdfParse->load('aiModel'),
        ]);
    }

    public function destroy(PdfParse $pdfParse)
    {
        $user = Auth::user();
        if ($pdfParse->user_id !== $user->id && !$user->isSuper()) {
            abort(403);
        }

        $pdfParse->delete();

        return back()->with('success', 'PDF parse record deleted successfully.');
    }
}
