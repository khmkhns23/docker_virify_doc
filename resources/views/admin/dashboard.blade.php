@extends('layouts.app')

@section('title', 'แผงควบคุมระบบ')
@section('page_title', 'ระบบตรวจสอบเอกสาร (แผงควบคุมผู้ดูแลระบบ)')

@section('content')
<!-- System Stats Grid -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card card-premium card-stats shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="small font-weight-bold text-navy text-uppercase mb-1">เอกสารทั้งหมดในระบบ</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-server fa-2x text-navy opacity-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card card-premium card-stats stats-completed shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="small font-weight-bold text-success text-uppercase mb-1">พบข้อความ (Found)</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['found'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-check-double fa-2x text-success opacity-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card card-premium card-stats stats-failed shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="small font-weight-bold text-danger text-uppercase mb-1">ไม่พบข้อความ (Not Found)</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['not_found'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-xmark fa-2x text-danger opacity-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card card-premium card-stats stats-processing shadow-sm h-100 py-2" style="border-left-color: #6c757d;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="small font-weight-bold text-muted text-uppercase mb-1">ผู้ใช้งานทั้งหมด</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['users_count'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-users fa-2x text-secondary opacity-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Document List -->
<div class="card card-premium shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="m-0 font-weight-bold text-navy"><i class="fa-solid fa-file-shield me-2"></i>รายการและสถานะตรวจสอบเอกสารของระบบ</h5>
    </div>
    <div class="card-body">
        <!-- Search & Filter Form -->
        <form action="{{ route('admin.dashboard') }}" method="GET" class="row g-3 mb-4 border-bottom pb-4">
            <div class="col-md-4">
                <label for="search" class="form-label small fw-bold text-navy">ค้นหาเอกสาร หรือ ผู้ใช้</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" id="search" class="form-control" placeholder="ค้นหาชื่อเอกสาร, ชื่อผู้ใช้, อีเมล..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label small fw-bold text-navy">สถานะคิวงาน</label>
                <select name="status" id="status" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอคิว (Pending)</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>กำลังประมวลผล (Processing)</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>เสร็จสิ้น (Completed)</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>ล้มเหลว (Failed)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="result_status" class="form-label small fw-bold text-navy">ผลลัพธ์การตรวจ</label>
                <select name="result_status" id="result_status" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="found" {{ request('result_status') == 'found' ? 'selected' : '' }}>พบข้อมูลตามเงื่อนไข (Found)</option>
                    <option value="not_found" {{ request('result_status') == 'not_found' ? 'selected' : '' }}>ไม่พบข้อมูลตามเงื่อนไข (Not Found)</option>
                    <option value="unknown" {{ request('result_status') == 'unknown' ? 'selected' : '' }}>ไม่ระบุ (Unknown)</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <div class="d-grid gap-2 w-100">
                    <button type="submit" class="btn btn-navy"><i class="fa-solid fa-filter me-1"></i> คัดกรอง</button>
                </div>
            </div>
        </form>

        <!-- Documents Table -->
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 table-premium">
                <thead>
                    <tr>
                        <th class="ps-4">ชื่อเอกสาร</th>
                        <th>ผู้ส่งตรวจ</th>
                        <th>เงื่อนไขตรวจสอบ</th>
                        <th>สถานะ</th>
                        <th>ผลลัพธ์</th>
                        <th>ส่งเมื่อ</th>
                        <th class="pe-4 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @if($documents->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-circle-question fa-3x mb-3 opacity-30"></i>
                                <h6>ไม่พบรายการเอกสารในระบบที่สอดคล้องกับการคัดกรอง</h6>
                            </td>
                        </tr>
                    @else
                        @foreach($documents as $doc)
                            <tr>
                                <td class="ps-4 font-weight-semibold">
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $doc->title }}">
                                        {{ $doc->title }}
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $doc->user->name }}</div>
                                    <small class="text-muted" style="font-size: 11px;">{{ $doc->user->email }}</small>
                                </td>
                                <td>
                                    <div class="text-truncate text-muted" style="max-width: 150px;" title="{{ $doc->target_text }}">
                                        {{ $doc->target_text }}
                                    </div>
                                </td>
                                <td>
                                    @if($doc->status === 'pending')
                                        <span class="badge bg-warning badge-status text-dark"><i class="fa-regular fa-clock me-1"></i> รอคิว</span>
                                    @elseif($doc->status === 'processing')
                                        <span class="badge bg-info badge-status text-white"><i class="fa-solid fa-spinner fa-spin me-1"></i> วิเคราะห์...</span>
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
                                        <!-- AJAX modal detail view -->
                                        <button class="btn btn-outline-navy btn-admin-details" 
                                                data-id="{{ $doc->id }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#adminDetailModal">
                                            <i class="fa-solid fa-eye me-1"></i> ดูรายละเอียด
                                        </button>
                                        
                                        <!-- View private PDF file -->
                                        <a href="{{ route('documents.view', $doc->id) }}" target="_blank" class="btn btn-outline-secondary" title="ดูไฟล์ PDF ต้นฉบับ">
                                            <i class="fa-regular fa-file-pdf"></i>
                                        </a>

                                        @if($doc->status === 'completed')
                                            <!-- Print Report -->
                                            <a href="{{ route('documents.report', ['id' => $doc->id, 'format' => 'print']) }}" target="_blank" class="btn btn-outline-dark" title="พิมพ์รายงานผล">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-center">
            {{ $documents->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Admin Details Modal (Dynamically Populated via AJAX) -->
<div class="modal fade" id="adminDetailModal" tabindex="-1" aria-labelledby="adminDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title" id="adminDetailModalLabel"><i class="fa-solid fa-circle-info me-2"></i>ผลลัพธ์การประมวลผลของระบบ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 position-relative">
                <!-- Loading Spinner -->
                <div id="modal-loading" class="position-absolute top-50 start-50 translate-middle text-center bg-white w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="z-index: 10; border-radius: 12px;">
                    <div class="spinner-border text-navy mb-2" role="status" style="width: 3rem; height: 3rem;"></div>
                    <span class="text-navy fw-semibold">กำลังโหลดข้อมูลและสถิติจากเซิร์ฟเวอร์...</span>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">ชื่อรายการเอกสาร</label>
                        <p id="admin-modal-title" class="mb-0 fw-semibold text-dark"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">ส่งตรวจสอบเมื่อ</label>
                        <p id="admin-modal-date" class="mb-0 text-muted"></p>
                    </div>
                </div>

                <div class="row mb-3 border-top pt-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">ผู้ส่งตรวจ (User)</label>
                        <p id="admin-modal-user" class="mb-0 text-dark fw-semibold"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">อีเมลผู้ส่ง (Email)</label>
                        <p id="admin-modal-email" class="mb-0 text-muted"></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-navy small">ข้อความหรือเงื่อนไขที่ตรวจค้น (Target Query)</label>
                    <div class="p-2 bg-light rounded text-navy border-start border-3 border-primary" id="admin-modal-target" style="white-space: pre-wrap;"></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">สถานะระบบ</label>
                        <div id="admin-modal-status-badge"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-navy small">ผลลัพธ์จาก AI (Ollama)</label>
                        <div id="admin-modal-result-badge"></div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Summary Section -->
                <div class="mb-3" id="admin-modal-summary-section">
                    <label class="fw-bold text-navy small"><i class="fa-solid fa-comment-dots text-success me-1"></i> สรุปใจความสำคัญของรายงานการประชุม (โดย AI)</label>
                    <div class="p-3 bg-light rounded shadow-sm border" id="admin-modal-summary" style="text-align: justify; line-height: 1.6;"></div>
                </div>

                <!-- Analysis Details Section -->
                <div class="mb-3" id="admin-modal-details-section">
                    <label class="fw-bold text-navy small"><i class="fa-solid fa-magnifying-glass-chart text-info me-1"></i> รายละเอียดคำชี้แจงการตรวจพบข้อมูล</label>
                    <div class="p-3 bg-light rounded shadow-sm border" id="admin-modal-details" style="text-align: justify; line-height: 1.6; white-space: pre-wrap;"></div>
                </div>

                <!-- Error Message Section -->
                <div class="mb-3 d-none" id="admin-modal-error-section">
                    <label class="fw-bold text-danger small"><i class="fa-solid fa-triangle-exclamation me-1"></i> ข้อมูลข้อยกเว้นล้มเหลว (Exception Message)</label>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded border border-danger font-monospace" id="admin-modal-error"></div>
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
    $(document).ready(function() {
        $('.btn-admin-details').on('click', function() {
            const docId = $(this).data('id');
            
            // Show loading spinner
            $('#modal-loading').removeClass('d-none');
            
            // Perform Ajax GET request to load details
            $.ajax({
                url: `/admin/documents/${docId}/details`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Populate modal fields
                    $('#admin-modal-title').text(data.title);
                    $('#admin-modal-date').text(data.created_at + ' น.');
                    
                    $('#admin-modal-user').text(data.user);
                    $('#admin-modal-email').text(data.email);
                    
                    $('#admin-modal-target').text(data.target_text);

                    // Set Status Badge
                    let statusHtml = '';
                    if (data.status === 'pending') {
                        statusHtml = '<span class="badge bg-warning text-dark"><i class="fa-regular fa-clock me-1"></i> รอคิวตรวจสอบ</span>';
                    } else if (data.status === 'processing') {
                        statusHtml = '<span class="badge bg-info text-white"><i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังตรวจสอบ...</span>';
                    } else if (data.status === 'completed') {
                        statusHtml = '<span class="badge bg-success text-white"><i class="fa-solid fa-circle-check me-1"></i> เสร็จสิ้น</span>';
                    } else if (data.status === 'failed') {
                        statusHtml = '<span class="badge bg-danger text-white"><i class="fa-solid fa-circle-xmark me-1"></i> ล้มเหลว</span>';
                    }
                    $('#admin-modal-status-badge').html(statusHtml);

                    // Set Result Badge
                    let resultHtml = '';
                    if (data.status === 'completed') {
                        if (data.result_status === 'found') {
                            resultHtml = '<span class="badge bg-success text-white"><i class="fa-solid fa-check me-1"></i> พบคำที่ตรวจค้น</span>';
                        } else if (data.result_status === 'not_found') {
                            resultHtml = '<span class="badge bg-danger text-white"><i class="fa-solid fa-times me-1"></i> ไม่พบคำที่ตรวจค้น</span>';
                        } else {
                            resultHtml = '<span class="badge bg-secondary text-white">ไม่ระบุ</span>';
                        }
                    } else {
                        resultHtml = '<span class="text-muted small">รอระบบประมวลผล</span>';
                    }
                    $('#admin-modal-result-badge').html(resultHtml);

                    // Handle content section displays
                    if (data.status === 'failed') {
                        $('#admin-modal-error-section').removeClass('d-none');
                        $('#admin-modal-error').text(data.error_message || 'ไม่ทราบสาเหตุการล้มเหลว');
                        
                        $('#admin-modal-summary-section').addClass('d-none');
                        $('#admin-modal-details-section').addClass('d-none');
                    } else if (data.status === 'completed') {
                        $('#admin-modal-error-section').addClass('d-none');
                        
                        $('#admin-modal-summary-section').removeClass('d-none');
                        $('#admin-modal-summary').text(data.summary);

                        $('#admin-modal-details-section').removeClass('d-none');
                        $('#admin-modal-details').text(data.details);
                    } else {
                        // Pending / Processing
                        $('#admin-modal-error-section').addClass('d-none');
                        
                        $('#admin-modal-summary-section').removeClass('d-none');
                        $('#admin-modal-summary').html('<span class="text-muted italic"><i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังรอสรุปผลจาก AI...</span>');

                        $('#admin-modal-details-section').removeClass('d-none');
                        $('#admin-modal-details').html('<span class="text-muted italic"><i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังวิเคราะห์ผลเจาะลึกจาก AI...</span>');
                    }

                    // Hide loading spinner
                    $('#modal-loading').addClass('d-none');
                },
                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ดึงข้อมูลล้มเหลว',
                        text: 'ไม่สามารถดึงข้อมูลรายละเอียดจากระบบได้ กรุณาลองอีกครั้ง'
                    });
                    $('#adminDetailModal').modal('hide');
                }
            });
        });
    });
</script>
@endsection
