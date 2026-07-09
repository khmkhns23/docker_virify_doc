@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, #1d3557 0%, #0f1c2e 100%);">
    <div class="card card-premium p-4 shadow-lg" style="width: 100%; max-width: 420px; border-radius: 16px;">
        <div class="card-body">
            <!-- Brand Logo / Name -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-navy text-white rounded-circle mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <h3 class="font-weight-bold text-navy mb-1">DocVerify</h3>
                <p class="text-muted small">ระบบตรวจสอบรายงานการประชุมและเอกสารราชการ</p>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-navy">อีเมลผู้ใช้งาน</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold text-navy">รหัสผ่าน</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label text-muted small">จดจำฉันในระบบ</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-navy py-2">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> เข้าสู่ระบบ
                    </button>
                </div>
            </form>

            <div class="text-center mt-4 border-top pt-3">
                <span class="text-muted small">ยังไม่มีบัญชีผู้ใช้?</span>
                <a href="{{ route('register') }}" class="text-navy small fw-semibold text-decoration-none ms-1">สมัครสมาชิกที่นี่</a>
            </div>
        </div>
    </div>
</div>
@endsection
