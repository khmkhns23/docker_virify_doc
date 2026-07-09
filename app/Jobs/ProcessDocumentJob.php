<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\OllamaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     * Ollama analysis can take some time on CPU, so we set a generous timeout.
     *
     * @var int
     */
    public $timeout = 300;

    protected $document;

    /**
     * Create a new job instance.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     */
    public function handle(OllamaService $ollamaService): void
    {
        $document = $this->document;

        // 1. Update status to processing
        $document->update([
            'status' => 'processing',
            'error_message' => null,
        ]);

        try {
            // 2. Check if file exists in private storage
            if (!Storage::exists($document->file_path)) {
                throw new \Exception("ไม่พบไฟล์เอกสารในระบบจัดเก็บไฟล์: " . $document->file_path);
            }

            // 3. Extract text from PDF
            Log::info("Extracting text from PDF for Document ID: " . $document->id);
            $filePath = Storage::path($document->file_path);

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            // Clean up text slightly (remove excess whitespace)
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            if (empty($text)) {
                throw new \Exception("ไม่สามารถสกัดข้อความออกจากไฟล์ PDF ได้ หรือเอกสารเป็นไฟล์สแกน (Image-only PDF) ที่ไม่มีเนื้อหาข้อความ");
            }

            Log::info("Text extracted successfully (" . mb_strlen($text) . " characters). Sending to Ollama...");

            // 4. Send text to Ollama Service
            $analysis = $ollamaService->verifyDocument($text, $document->target_text);

            if ($analysis['success']) {
                $document->update([
                    'status' => 'completed',
                    'result_status' => $analysis['result_status'],
                    'summary' => $analysis['summary'],
                    'details' => $analysis['details'],
                ]);
                Log::info("Document verification completed for ID: " . $document->id);
            } else {
                throw new \Exception($analysis['error'] ?? "เกิดข้อผิดพลาดในการวิเคราะห์ด้วย Ollama");
            }

        } catch (\Exception $e) {
            Log::error("Error processing Document ID " . $document->id . ": " . $e->getMessage());
            
            $document->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
