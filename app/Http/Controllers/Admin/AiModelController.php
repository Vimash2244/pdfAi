<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AiModelController extends Controller
{
    public function index(): Response
    {
        $aiModels = AiModel::latest()->paginate(20);

        return Inertia::render('Admin/AiModels/Index', [
            'aiModels' => $aiModels,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/AiModels/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model_identifier' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'config.api_key' => 'required|string',
            'config.endpoint' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Ensure config is properly structured
        if (empty($data['config']['endpoint'])) {
            unset($data['config']['endpoint']);
        }

        AiModel::create($data);

        return redirect()->route('admin.ai-models.index')
            ->with('success', 'AI Model created successfully.');
    }

    public function edit(AiModel $aiModel): Response
    {
        return Inertia::render('Admin/AiModels/Edit', [
            'aiModel' => $aiModel,
        ]);
    }

    public function update(Request $request, AiModel $aiModel)
    {
        Log::info('AI Model update request received', [
            'ai_model_id' => $aiModel->id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'model_identifier' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'config.api_key' => 'nullable|string',
            'config.endpoint' => 'nullable|string',
        ]);

        $data = $request->all();
        
        Log::info('Validation passed, processing data', ['data' => $data]);
        
        // Handle config properly
        if (isset($data['config'])) {
            // If API key is empty, don't update it (keep existing)
            if (empty($data['config']['api_key'])) {
                unset($data['config']['api_key']);
            }
            
            // If endpoint is empty, don't update it (keep existing)
            if (empty($data['config']['endpoint'])) {
                unset($data['config']['endpoint']);
            }
            
            // If config is completely empty, set it to null
            if (empty($data['config'])) {
                $data['config'] = null;
            }
        }

        Log::info('Final data to update', ['final_data' => $data]);

        $aiModel->update($data);

        Log::info('AI Model updated successfully', ['ai_model_id' => $aiModel->id]);

        return redirect()->route('admin.ai-models.index')
            ->with('success', 'AI Model updated successfully.');
    }

    public function toggleStatus(AiModel $ai_model)
    {
        $ai_model->update([
            'is_active' => !$ai_model->is_active
        ]);

        return redirect()->route('admin.ai-models.index')
            ->with('success', 'AI Model status updated successfully.');
    }

    public function destroy(AiModel $aiModel)
    {
        $aiModel->delete();

        return redirect()->route('admin.ai-models.index')
            ->with('success', 'AI Model deleted successfully.');
    }
}
