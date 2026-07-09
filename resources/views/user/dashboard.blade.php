@extends('layouts.app')

@section('title', 'แดชบอร์ดของฉัน')
@section('page_title', 'แดชบอร์ดจัดการเอกสาร')

@section('content')
<!-- Stats Widgets -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card card-premium card-stats shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-navy text-uppercase mb-1">เอกสารทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-folder-open fa-2x text-navy opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card card-premium card-stats stats-completed shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">พบข้อความเงื่อนไข</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['found'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-file-circle-check fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-premium card-stats stats-failed shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">ไม่พบข้อความเงื่อนไข</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['not_found'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-file-circle-xmark fa-2x text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-premium card-stats stats-processing shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">อยู่ระหว่างการตรวจสอบ</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] + $stats['processing'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-spinner fa-2x text-info fa-spin opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upload Document Card -->
    <div class="col-lg-4 mb-4">
        <div class="card card-premium shadow-sm">
            <div class="card-header bg-navy text-white d-flex align-items-center">
                <i class="fa-solid fa-file-arrow-up me-2"></i> อัพโหลดเอกสารใหม่
            </div>
            <div class="card-body">
                <form action="{{ route('user.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold text-navy">ชื่อรายการ / หัวข้อเอกสาร</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="เช่น รายงานการประชุมคณะทำงาน ครั้งที่ 1" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="target_text" class="form-label fw-semibold text-navy">เงื่อนไข/คำที่ต้องการตรวจสอบ</label>
                        <textarea name="target_text" id="target_text" class="form-control @error('target_text') is-invalid @enderror" rows="3" placeholder="เช่น ตรวจหาคำว่า 'อนุมัติงบประมาณ 50,000 บาท' หรือ 'ข้อเสนอของคณะทำงาน'" required>{{ old('target_text') }}</textarea>
                        @error('target_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-navy">เลือกไฟล์รายงานการประชุม (PDF เท่านั้น)</label>
                        <div class="upload-zone" onclick="document.getElementById('document-input').click()">
                            <i class="fa-regular fa-file-pdf fa-3x text-navy mb-2 opacity-75"></i>
                            <p class="mb-1 text-navy fw-semibold">คลิกเพื่อเลือกไฟล์ PDF</p>
                            <span class="text-muted small">ขนาดไฟล์ต้องไม่เกิน 10 MB</span>
                            <input type="file" name="document" id="document-input" class="d-none @error('document') is-invalid @enderror" accept=".pdf" required onchange="updateFileName(this)">
                        </div>
                        <div id="file-name-display" class="mt-2 text-success small font-weight-bold text-center" style="display:none;"></div>
                        @error('document')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-navy py-2">
                            <i class="fa-solid fa-circle-check me-2"></i> ส่งตรวจสอบเอกสาร
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Document List Card -->
    <div class="col-lg-8 mb-4">
        <div class="card card-premium shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-navy"><i class="fa-solid fa-list-check me-2"></i>รายการเอกสารของฉัน</span>
                <span class="badge bg-navy">{{ $documents->total() }} รายการ</span>
            </div>
            <div class="card-body p-0">
                @if($documents->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa-solid fa-file-lines fa-3x text-muted opacity-50 mb-3"></i>
                        <h6 class="text-muted">ยังไม่มีเอกสารอัพโหลดในระบบ</h6>
                        <p class="text-muted small">คุณสามารถอัพโหลดเอกสารเพื่อส่งให้ AI ตรวจสอบได้ทางด้านซ้าย</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 table-premium">
                            <thead>
                                <tr>
                                    <th class="ps-4">ชื่อเอกสาร</th>
                                    <th>เงื่อนไขตรวจสอบ</th>
                                    <th>สถานะ</th>
                                    <th>ผลลัพธ์</th>
                                    <th>วันที่อัพโหลด</th>
                                    <th class="pe-4 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $doc)
                                    <tr>
                                        <td class="ps-4 font-weight-semibold">
                                            <div class="text-truncate" style="max-width: 180px;" title="{{ $doc->title }}">
                                                {{ $doc->title }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate text-muted" style="max-width: 140px;" title="{{ $doc->target_text }}">
                                                {{ $doc->target_text }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($doc->status === 'pending')
                                                <span class="badge bg-warning badge-status text-dark"><i class="fa-regular fa-clock me-1"></i> รอคิว</span>
                                            @elseif($doc->status === 'processing')
                                                <span class="badge bg-info badge-status text-white"><i class="fa-solid fa-spinner fa-spin me-1"></i> ตรวจสอบ...</span>
                                            @elseif($doc->status === 'completed')
                                                <span class="badge bg-success badge-status"><i class="fa-solid fa-circle-check me-1"></i> เสร็จสิ้น</span>
                                            @elseif($doc->status === 'failed')
                                                <span class="badge bg-danger badge-status"><i class="fa-solid fa-circle-xmark me-1"></i> ล้มเหลว</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->status === 'completed')
                                                @if($doc->result_status === 'found')
                                                    <span class="badge bg-success badge-status"><i class="fa-solid fa-square-check me-1"></i> พบ</span>
                                                @elseif($doc->result_status === 'not_found')
                                                    <span class="badge bg-danger badge-status"><i class="fa-solid fa-square-xmark me-1"></i> ไม่พบ</span>
                                                @else
                                                    <span class="badge bg-secondary badge-status">ไม่ระบุ</span>
                                                @endif
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="small">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Action view details -->
                                                <button class="btn btn-outline-navy btn-view-details" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal"
                                                        data-id="{{ $doc->id }}"
                                                        data-title="{{ $doc->title }}"
                                                        data-target="{{ $doc->target_text }}"
                                                        data-status="{{ $doc->status }}"
                                                        data-result="{{ $doc->result_status }}"
                                                        data-summary="{{ $doc->summary }}"
                                                        data-details="{{ $doc->details }}"
                                                        data-error="{{ $doc->error_message }}"
                                                        data-date="{{ $doc->created_at->format('d/m/Y H:i:s') }}">
                                                    <i class="fa-solid fa-eye"></i> ดูผล
                                                </button>
                                                
                                                <!-- View private file -->
                                                <a href="{{ route('documents.view', $doc->id) }}" target="_blank" class="btn btn-outline-secondary" title="ดูไฟล์ PDF ต้นฉบับ">
                                                    <i class="fa-regular fa-file-pdf"></i>
                                                </a>

                                                @if($doc->status === 'completed')
                                                    <!-- Export CSV -->
                                                    <a href="{{ route('documents.report', ['id' => $doc->id, 'format' => 'csv']) }}" class="btn btn-outline-success" title="ส่งออกรายงาน CSV">
                                                        <i class="fa-solid fa-file-excel"></i>
                                                    </a>
                                                    
                                                    <!-- Print Details -->
                                                    <a href="{{ route('documents.report', ['id' => $doc->id, 'format' => 'print']) }}" target="_blank" class="btn btn-outline-dark" title="พิมพ์รายงาน">
                                                        <i class="fa-solid fa-print"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-3 border-top d-flex justify-content-center">
                        {{ $documents->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title" id="detailModalLabel"><i class="fa-solid fa-file-magnifying-glass me-2"></i>รายละเอียดผลการตรวจสอบ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">ชื่อเอกสาร</label>
                        <p id="modal-title" class="mb-0 fw-semibold text-dark"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">วันที่อัพโหลด</label>
                        <p id="modal-date" class="mb-0 text-muted"></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-navy small">เงื่อนไข/ข้อความที่ตรวจค้น</label>
                    <div class="p-2 bg-light rounded text-navy border-start border-3 border-primary" id="modal-target" style="white-space: pre-wrap;"></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">สถานะการทำงาน</label>
                        <div id="modal-status-badge"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">ผลการวิเคราะห์</label>
                        <div id="modal-result-badge"></div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Summary Section -->
                <div class="mb-3" id="modal-summary-section">
                    <label class="fw-bold text-navy small"><i class="fa-solid fa-comment-dots me-1 text-success"></i> สรุปใจความสำคัญเอกสาร (โดย Ollama)</label>
                    <div class="p-3 bg-light rounded shadow-sm border" id="modal-summary" style="text-align: justify; line-height: 1.6;"></div>
                </div>

                <!-- Analysis Details Section -->
                <div class="mb-3" id="modal-details-section">
                    <label class="fw-bold text-navy small"><i class="fa-solid fa-magnifying-glass-chart me-1 text-info"></i> รายละเอียดการวิเคราะห์ค้นหาสิ่งที่ต้องการ</label>
                    <div class="p-3 bg-light rounded shadow-sm border" id="modal-details" style="text-align: justify; line-height: 1.6; white-space: pre-wrap;"></div>
                </div>

                <!-- Error Message Section -->
                <div class="mb-3 d-none" id="modal-error-section">
                    <label class="fw-bold text-danger small"><i class="fa-solid fa-triangle-exclamation me-1"></i> สาเหตุที่ล้มเหลว</label>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded border border-danger" id="modal-error"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.innerText = "ไฟล์ที่เลือก: " + input.files[0].name;
            display.style.display = "block";
        } else {
            display.style.display = "none";
        }
    }

    $(document).ready(function() {
        // Handle detail view buttons
        $('.btn-view-details').on('click', function() {
            const btn = $(this);
            const title = btn.data('title');
            const target = btn.data('target');
            const status = btn.data('status');
            const result = btn.data('result');
            const summary = btn.data('summary');
            const details = btn.data('details');
            const error = btn.data('error');
            const date = btn.data('date');

            // Populate Modal Text
            $('#modal-title').text(title);
            $('#modal-date').text(date);
            $('#modal-target').text(target);
            
            // Set Status Badge
            let statusHtml = '';
            if (status === 'pending') {
                statusHtml = '<span class="badge bg-warning text-dark"><i class="fa-regular fa-clock me-1"></i> รอคิวการประมวลผล</span>';
            } else if (status === 'processing') {
                statusHtml = '<span class="badge bg-info text-white"><i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังวิเคราะห์เอกสาร...</span>';
            } else if (status === 'completed') {
                statusHtml = '<span class="badge bg-success text-white"><i class="fa-solid fa-circle-check me-1"></i> ตรวจสอบเสร็จสิ้น</span>';
            } else if (status === 'failed') {
                statusHtml = '<span class="badge bg-danger text-white"><i class="fa-solid fa-circle-xmark me-1"></i> ล้มเหลว</span>';
            }
            $('#modal-status-badge').html(statusHtml);

            // Set Result Badge
            let resultHtml = '';
            if (status === 'completed') {
                if (result === 'found') {
                    resultHtml = '<span class="badge bg-success text-white"><i class="fa-solid fa-check me-1"></i> พบข้อความตรงตามเงื่อนไข</span>';
                } else if (result === 'not_found') {
                    resultHtml = '<span class="badge bg-danger text-white"><i class="fa-solid fa-times me-1"></i> ไม่พบข้อความตรงตามเงื่อนไข</span>';
                } else {
                    resultHtml = '<span class="badge bg-secondary text-white">ไม่ระบุ</span>';
                }
            } else {
                resultHtml = '<span class="text-muted small">รอการตรวจสอบเสร็จสิ้น</span>';
            }
            $('#modal-result-badge').html(resultHtml);

            // Handle content section displays
            if (status === 'failed') {
                $('#modal-error-section').removeClass('d-none');
                $('#modal-error').text(error || 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ');
                $('#modal-summary-section').addClass('d-none');
                $('#modal-details-section').addClass('d-none');
            } else if (status === 'completed') {
                $('#modal-error-section').addClass('d-none');
                
                $('#modal-summary-section').removeClass('d-none');
                $('#modal-summary').text(summary || 'ไม่มีข้อความสรุป');

                $('#modal-details-section').removeClass('d-none');
                $('#modal-details').text(details || 'ไม่มีรายละเอียดผลลัพธ์');
            } else {
                // Pending or Processing
                $('#modal-error-section').addClass('d-none');
                
                $('#modal-summary-section').removeClass('d-none');
                $('#modal-summary').html('<span class="text-muted italic"><i class="fa-solid fa-circle-notch fa-spin me-1"></i> ระบบกำลังส่งข้อมูลให้ Ollama ประมวลผลผลลัพธ์ โปรดรอสักครู่...</span>');
                
                $('#modal-details-section').removeClass('d-none');
                $('#modal-details').html('<span class="text-muted italic"><i class="fa-solid fa-circle-notch fa-spin me-1"></i> ระบบกำลังวิเคราะห์รายละเอียดเนื้อหา โปรดรอสักครู่...</span>');
            }
        });
    });
</script>
@endsection
