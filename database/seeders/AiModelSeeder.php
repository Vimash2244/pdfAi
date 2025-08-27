<?php

namespace Database\Seeders;

use App\Models\AiModel;
use Illuminate\Database\Seeder;

class AiModelSeeder extends Seeder
{
    public function run(): void
    {
        $aiModels = [
            [
                'name' => 'OpenAI',
                'model_identifier' => 'gpt-4',
                'description' => 'OpenAI GPT-4 model for advanced PDF parsing',
                'is_active' => true,
                'config' => [
                    'api_key' => env('OPENAI_API_KEY'),
                    'endpoint' => 'https://api.openai.com/v1/chat/completions',
                ],
            ],
            [
                'name' => 'Gemini',
                'model_identifier' => 'gemini-2.0-flash',
                'description' => 'Google Gemini 1.5 Pro model for PDF parsing',
                'is_active' => true,
                'config' => [
                    'api_key' => env('GEMINI_API_KEY'),
                    'endpoint' => 'https://generativelanguage.googleapis.com/v1',
                ],
            ],
        ];

        foreach ($aiModels as $aiModel) {
            AiModel::create($aiModel);
        }
    }
}
