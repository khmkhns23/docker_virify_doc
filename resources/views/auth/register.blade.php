@extends('layouts.app')

@section('title', 'สมัครสมาชิก')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, #1d3557 0%, #0f1c2e 100%);">
    <div class="card card-premium p-4 shadow-lg" style="width: 100%; max-width: 460px; border-radius: 16px;">
        <div class="card-body">
            <!-- Brand Logo / Name -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-navy text-white rounded-circle mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h3 class="font-weight-bold text-navy mb-1">สมัครสมาชิก</h3>
                <p class="text-muted small">สมัครบัญชีผู้ใช้ใหม่สำหรับระบบ DocVerify</p>
            </div>

            <!-- Registration Form -->
            <form action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold text-navy">ชื่อ-นามสกุล</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="name" id="name" class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" placeholder="สมชาย ใจดี" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-navy">อีเมลผู้ใช้งาน</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required>
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
                        <input type="password" name="password" id="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" placeholder="รหัสผ่านอย่างน้อย 8 ตัวอักษร" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold text-navy">ยืนยันรหัสผ่านอีกครั้ง</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock-open"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-light border-start-0" placeholder="ยืนยันรหัสผ่าน" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-navy py-2">
                        <i class="fa-solid fa-user-plus me-2"></i> ลงทะเบียนและเข้าระบบ
                    </button>
                </div>
            </form>

            <div class="text-center mt-4 border-top pt-3">
                <span class="text-muted small">มีบัญชีผู้ใช้อยู่แล้ว?</span>
                <a href="{{ route('login') }}" class="text-navy small fw-semibold text-decoration-none ms-1">เข้าสู่ระบบที่นี่</a>
            </div>
        </div>
    </div>
</div>
@endsection
