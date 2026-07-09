<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Jobs\ProcessDocumentJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard with documents.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Retrieve user's documents
        $documents = Document::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate statistics
        $stats = [
            'total' => Document::where('user_id', $user->id)->count(),
            'pending' => Document::where('user_id', $user->id)->where('status', 'pending')->count(),
            'processing' => Document::where('user_id', $user->id)->where('status', 'processing')->count(),
            'completed' => Document::where('user_id', $user->id)->where('status', 'completed')->count(),
            'failed' => Document::where('user_id', $user->id)->where('status', 'failed')->count(),
            'found' => Document::where('user_id', $user->id)->where('result_status', 'found')->count(),
            'not_found' => Document::where('user_id', $user->id)->where('result_status', 'not_found')->count(),
        ];

        return view('user.dashboard', compact('documents', 'stats'));
    }

    /**
     * Handle document upload, validation, and queue dispatch.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_text' => ['required', 'string'],
            'document' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB limit
        ], [
            'title.required' => 'กรุณากรอกชื่อเอกสาร',
            'target_text.required' => 'กรุณาระบุข้อความหรือเงื่อนไขที่ต้องการตรวจสอบ',
            'document.required' => 'กรุณาเลือกไฟล์เอกสาร PDF',
            'document.mimes' => 'ไฟล์ที่อัพโหลดจะต้องเป็นไฟล์ PDF เท่านั้น',
            'document.max' => 'ขนาดไฟล์ต้องไม่เกิน 10MB',
        ]);

        try {
            $user = Auth::user();
            $file = $request->file('document');

            // Store file securely in local private storage 'storage/app/documents'
            $path = $file->store('documents');

            if (!$path) {
                throw new \Exception("ไม่สามารถบันทึกไฟล์ลงเซิร์ฟเวอร์ได้");
            }

            // Create document record with pending status
            $document = Document::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'file_path' => $path,
                'target_text' => $request->target_text,
                'status' => 'pending',
                'result_status' => 'unknown',
            ]);

            // Dispatch background processing job
            ProcessDocumentJob::dispatch($document);

            return redirect()->route('user.dashboard')->with('success', 'อัพโหลดเอกสารสำเร็จ! ระบบเข้าคิวประมวลผลการตรวจสอบแล้ว');

        } catch (\Exception $e) {
            Log::error("Upload failed: " . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการอัพโหลด: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Securely serve/view the PDF file inline.
     */
    public function viewFile($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // Security check: Must be owner or admin
        if ($document->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'ไม่มีสิทธิ์ในการเข้าถึงเอกสารนี้');
        }

        if (!Storage::exists($document->file_path)) {
            abort(404, 'ไม่พบไฟล์เอกสารบนเซิร์ฟเวอร์');
        }

        $filePath = Storage::path($document->file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"',
        ]);
    }

    /**
     * Export reports (CSV / HTML Print view).
     */
    public function exportReport($id, $format)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        // Security Check: Must be owner or admin
        if ($document->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงรายงานของเอกสารนี้');
        }

        if ($format === 'csv') {
            $fileName = 'document_report_' . $document->id . '_' . date('Ymd_His') . '.csv';
            
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['ID', 'ชื่อเอกสาร', 'ผู้ตรวจ', 'เงื่อนไขที่ตรวจสอบ', 'สถานะการตรวจ', 'ผลการตรวจ', 'สรุปรายงาน', 'รายละเอียดผลการตรวจ', 'สร้างเมื่อ'];

            $callback = function() use($document, $columns) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM for Thai characters in Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, $columns);

                fputcsv($file, [
                    $document->id,
                    $document->title,
                    $document->user->name,
                    $document->target_text,
                    $document->status === 'completed' ? 'เสร็จสิ้น' : ($document->status === 'processing' ? 'กำลังประมวลผล' : ($document->status === 'failed' ? 'ล้มเหลว' : 'รอการตรวจสอบ')),
                    $document->result_status === 'found' ? 'พบข้อมูลตามเงื่อนไข' : ($document->result_status === 'not_found' ? 'ไม่พบข้อมูลตามเงื่อนไข' : 'ไม่ระบุ'),
                    $document->summary,
                    $document->details,
                    $document->created_at->format('Y-m-d H:i:s')
                ]);

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } elseif ($format === 'print') {
            return view('reports.print', compact('document'));
        }

        abort(404);
    }
}
