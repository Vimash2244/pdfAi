<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\PdfParse;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class PdfParseService
{
    public function parsePdf(User $user, $file, AiModel $aiModel): PdfParse
    {
        // Create PDF parse record
        $pdfParse = PdfParse::create([
            'user_id' => $user->id,
            'ai_model_id' => $aiModel->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $this->storeFile($file),
            'file_size_bytes' => $file->getSize(),
            'status' => 'processing',
        ]);

        try {
            // Parse PDF based on AI model
            $result = $this->parseWithAi($file, $aiModel);
            
            $pdfParse->update([
                'parse_result' => $result,
                'status' => 'completed',
                'processed_at' => now(),
            ]);

        } catch (\Exception $e) {
            $pdfParse->update([
                'error_message' => $e->getMessage(),
                'status' => 'failed',
            ]);
        }

        return $pdfParse;
    }

    private function storeFile($file): string
    {
        $filename = 'pdfs/' . Str::uuid() . '.pdf';
        Storage::disk('local')->put($filename, file_get_contents($file));
        return $filename;
    }

    private function parseWithAi($file, AiModel $aiModel): array
    {
        $pdfData = $this->extractPdfData($file);
        
        switch (strtolower($aiModel->name)) {
            case 'openai':
                return $this->parseWithOpenAI($pdfData, $aiModel);
            case 'gemini':
                return $this->parseWithGemini($pdfData, $aiModel);
            default:
                throw new \Exception("Unsupported AI model: {$aiModel->name}");
        }
    }

    private function extractPdfData($file): array
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getPathname());
            
            // Extract text content
            $text = $pdf->getText();
            
            // Extract metadata
            $details = $pdf->getDetails();
            
            // Extract pages information
            $pages = $pdf->getPages();
            $pageCount = count($pages);
            
            // Extract structured data
            $structuredData = [
                'text_content' => $text,
                'metadata' => [
                    'title' => $details['Title'] ?? null,
                    'author' => $details['Author'] ?? null,
                    'subject' => $details['Subject'] ?? null,
                    'creator' => $details['Creator'] ?? null,
                    'producer' => $details['Producer'] ?? null,
                    'creation_date' => $details['CreationDate'] ?? null,
                    'modification_date' => $details['ModDate'] ?? null,
                    'keywords' => $details['Keywords'] ?? null,
                ],
                'document_info' => [
                    'page_count' => $pageCount,
                    'file_size' => $file->getSize(),
                    'filename' => $file->getClientOriginalName(),
                ],
                'extraction_timestamp' => now()->toISOString(),
            ];
            
            return $structuredData;
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to parse PDF: " . $e->getMessage());
        }
    }

    private function parseWithOpenAI(array $pdfData, AiModel $aiModel): array
    {
        $systemPrompt = $this->getStructuredSystemPrompt();
        $userPrompt = $this->getStructuredUserPrompt($pdfData);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $aiModel->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $aiModel->model_identifier,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt
                ]
            ],
            'temperature' => 0.1, // Lower temperature for more consistent output
            'response_format' => ['type' => 'json_object'], // Ensure JSON response
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? '';
        
        $parsed = json_decode($content, true);
        
        if (!$parsed) {
            throw new \Exception('Failed to parse JSON response from OpenAI');
        }
        
        return [
            'raw_response' => $content,
            'parsed_data' => $parsed,
            'model_used' => $aiModel->model_identifier,
            'tokens_used' => $result['usage']['total_tokens'] ?? 0,
            'pdf_metadata' => $pdfData['metadata'],
            'extraction_info' => $pdfData['document_info'],
        ];
    }

    private function parseWithGemini(array $pdfData, AiModel $aiModel): array
    {
        $systemPrompt = $this->getStructuredSystemPrompt();
        $userPrompt = $this->getStructuredUserPrompt($pdfData);
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/' . $aiModel->model_identifier . ':generateContent?key=' . $aiModel->getApiKey(), [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $systemPrompt . "\n\n" . $userPrompt
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'responseMimeType' => 'application/json',
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Gemini API error: ' . $response->body());
        }

        $result = $response->json();
        $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        $parsed = json_decode($content, true);
        
        if (!$parsed) {
            throw new \Exception('Failed to parse JSON response from Gemini');
        }
        
        return [
            'raw_response' => $content,
            'parsed_data' => $parsed,
            'model_used' => $aiModel->model_identifier,
            'tokens_used' => 0, // Gemini doesn't provide token usage in the same way
            'pdf_metadata' => $pdfData['metadata'],
            'extraction_info' => $pdfData['document_info'],
        ];
    }

    private function getStructuredSystemPrompt(): string
    {
        return "You are a professional PDF parsing AI assistant. Your task is to analyze PDF content and extract structured information in a consistent JSON format.

        IMPORTANT: You MUST return ONLY valid JSON. Do not include any markdown formatting, explanations, or text outside the JSON structure.

        Ensure all fields are properly filled. If information is not available, use null or empty arrays as appropriate.";
    }

    private function getStructuredUserPrompt(array $pdfData): string
    {
        $text = $pdfData['text_content'];
        $metadata = $pdfData['metadata'];
        
        $prompt = "Please analyze the following PDF content and return structured information in the exact JSON format specified above.\n\n";
        
        if ($metadata['title']) {
            $prompt .= "Document Title: {$metadata['title']}\n";
        }
        if ($metadata['author']) {
            $prompt .= "Author: {$metadata['author']}\n";
        }
        if ($metadata['subject']) {
            $prompt .= "Subject: {$metadata['subject']}\n";
        }
        
        $prompt .= "\nPDF Content:\n";
        $prompt .= "Page Count: {$pdfData['document_info']['page_count']}\n";
        $prompt .= "File Size: {$pdfData['document_info']['file_size']} bytes\n\n";
        
        // Truncate text if too long to avoid token limits
        $maxLength = 8000; // Conservative limit
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . "\n\n[Content truncated due to length]";
        }
        
        $prompt .= "Extracted Text:\n{$text}\n\n";
        $prompt .= "Please return ONLY the JSON response following the exact structure specified above.";
        
        return $prompt;
    }
}
