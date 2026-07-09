<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานผลการตรวจสอบเอกสาร - {{ $document->title }}</title>
    
    <!-- Google Web Font Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #white;
            color: #000;
            margin: 0;
            padding: 30px;
            font-size: 16px;
            line-height: 1.6;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border-bottom: 2px solid #1d3557;
            padding-bottom: 15px;
        }

        .header-logo {
            font-size: 28px;
            font-weight: bold;
            color: #1d3557;
            text-align: left;
        }

        .header-title {
            text-align: right;
            font-size: 16px;
            color: #555;
        }

        h2 {
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
            color: #1d3557;
        }

        .info-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .info-box td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }

        .info-label {
            font-weight: bold;
            background-color: #f8f9fa;
            width: 25%;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #1d3557;
            border-left: 4px solid #1d3557;
            padding-left: 10px;
        }

        .content-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            background-color: #fafafa;
            margin-bottom: 20px;
            text-align: justify;
            white-space: pre-wrap;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 14px;
        }

        .badge-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .badge-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .badge-info {
            background-color: #e0f7fa;
            color: #00838f;
            border: 1px solid #b2ebf2;
        }

        .footer-sig {
            margin-top: 60px;
            width: 100%;
            border-collapse: collapse;
        }

        .footer-sig td {
            width: 50%;
            text-align: center;
        }

        .no-print-btn {
            background-color: #1d3557;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
            display: block;
            margin: 0 auto 30px auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .no-print-btn:hover {
            background-color: #112233;
        }

        @media print {
            .no-print-btn {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Print Button for manual clicking, hidden when printed -->
    <button class="no-print-btn" onclick="window.print()">
        <i class="fa-solid fa-print"></i> พิมพ์เอกสารรายงาน / บันทึกเป็น PDF
    </button>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="header-logo">DocVerify System</td>
            <td class="header-title">รายงานการประมวลผลข้อมูลปัญญาประดิษฐ์ (AI)</td>
        </tr>
    </table>

    <h2>รายงานผลการตรวจสอบเอกสารรายงานการประชุม</h2>

    <!-- Info Box -->
    <table class="info-box">
        <tr>
            <td class="info-label">รหัสเอกสาร</td>
            <td>DOC-{{ str_pad($document->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="info-label">วันที่ส่งตรวจ</td>
            <td>{{ $document->created_at->format('d/m/Y H:i:s') }} น.</td>
        </tr>
        <tr>
            <td class="info-label">ชื่อหัวข้อเอกสาร</td>
            <td>{{ $document->title }}</td>
            <td class="info-label">ผู้ส่งตรวจสอบ</td>
            <td>{{ $document->user->name }} ({{ $document->user->email }})</td>
        </tr>
        <tr>
            <td class="info-label">ผลการวิเคราะห์เงื่อนไข</td>
            <td colspan="3">
                @if($document->result_status === 'found')
                    <span class="badge badge-success">พบข้อมูลตามเงื่อนไขที่กำหนด</span>
                @elseif($document->result_status === 'not_found')
                    <span class="badge badge-danger">ไม่พบข้อมูลตามเงื่อนไขที่กำหนด</span>
                @else
                    <span class="badge badge-info">ไม่ระบุแน่ชัด</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- Target Text -->
    <div class="section-title">เงื่อนไข / ข้อความที่ต้องการตรวจสอบ</div>
    <div class="content-box" style="font-weight: bold; background-color: #eef2f7; border-left: 4px solid #1d3557;">{{ $document->target_text }}</div>

    <!-- Summary -->
    <div class="section-title">สรุปใจความสำคัญของเอกสาร (Generated by Ollama)</div>
    <div class="content-box">{{ $document->summary }}</div>

    <!-- Details -->
    <div class="section-title">รายละเอียดผลการตรวจสอบเจาะลึก</div>
    <div class="content-box" style="background-color: #fff;">{{ $document->details }}</div>

    <!-- Signature Fields -->
    <table class="footer-sig">
        <tr>
            <td>
                <br>
                ลงชื่อ..........................................................<br>
                ( {{ $document->user->name }} )<br>
                ผู้ส่งรายงานตรวจสอบ
            </td>
            <td>
                <br>
                ลงชื่อ..........................................................<br>
                ( ระบบผู้ดูแลระบบ )<br>
                ผู้อนุมัติผลการประมวลผลระบบ
            </td>
        </tr>
    </table>

    <script>
        // Auto trigger print prompt on load
        window.onload = function() {
            // Uncomment to auto print
            // window.print();
        }
    </script>
</body>
</html>
