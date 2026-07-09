<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        // We use the docker service name 'ollama' and port '11434'
        $this->baseUrl = env('OLLAMA_URL', 'http://ollama:11434');
        $this->model = env('OLLAMA_MODEL', 'qwen2.5:3b');
    }

    /**
     * Verify a document text with Ollama.
     *
     * @param string $documentText
     * @param string $targetText
     * @return array
     */
    public function verifyDocument(string $documentText, string $targetText): array
    {
        // Limit text length to prevent context window overload (roughly 8000 tokens / 30000 chars)
        $trimmedText = mb_substr($documentText, 0, 30000);

        $prompt = "You are a professional document auditor. Analyze the following document text (which is a Thai meeting minutes or report) and check if it satisfies the user request/condition.

Document Text:
\"\"\"
{$trimmedText}
\"\"\"

User Request (Condition to check):
\"\"\"
{$targetText}
\"\"\"

Instructions:
1. You must respond in Thai.
2. Determine if the condition is met. Output result_status as 'found' if the condition is explicitly found or verified, 'not_found' if it is definitely not present, and 'unknown' if it cannot be determined.
3. Provide a brief 2-3 sentence summary of the document in Thai in the 'summary' field.
4. Explain the details of your findings in Thai in the 'details' field. Indicate exactly where or how the condition was found or why it wasn't.

Return your response ONLY as a JSON object matching this structure:
{
  \"result_status\": \"found\" | \"not_found\" | \"unknown\",
  \"summary\": \"ข้อความสรุปรายงานการประชุมใน 2-3 ประโยค...\",
  \"details\": \"รายละเอียดผลการตรวจสอบ เช่น พบว่า... อยู่ในหัวข้อ...\"
}";

        try {
            $response = Http::timeout(300)->post("{$this->baseUrl}/api/generate", [
                'model' => $this->model,
                'prompt' => $prompt,
                'system' => 'You are a professional Thai document auditor. You only output valid JSON. Always respond in Thai.',
                'stream' => false,
                'format' => 'json',
                'options' => [
                    'temperature' => 0.1, // low temperature for consistent, analytical output
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $resultText = $data['response'] ?? '';
                
                Log::info("Ollama RAW Response: " . $resultText);
                
                $resultJson = json_decode($resultText, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return [
                        'success' => true,
                        'result_status' => $resultJson['result_status'] ?? 'unknown',
                        'summary' => $resultJson['summary'] ?? '',
                        'details' => $resultJson['details'] ?? '',
                    ];
                }
                
                // Fallback in case JSON is not parsed correctly but contains text
                return [
                    'success' => true,
                    'result_status' => 'unknown',
                    'summary' => mb_substr($resultText, 0, 200),
                    'details' => $resultText,
                ];
            }

            return [
                'success' => false,
                'error' => "Ollama returned status code: " . $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("Ollama connection failed: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Could not connect to Ollama: " . $e->getMessage(),
            ];
        }
    }
}
