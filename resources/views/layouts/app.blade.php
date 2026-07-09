<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบตรวจสอบเอกสาร') - TH Doc Verify</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            @auth
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 bg-navy text-white sidebar d-flex flex-column justify-content-between p-0">
                    <div>
                        <!-- Brand -->
                        <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
                            <h4 class="mb-0 text-white font-weight-bold">
                                <i class="fa-solid fa-file-shield me-2"></i>DocVerify
                            </h4>
                            <small class="text-white-50">ระบบตรวจสอบเอกสาร</small>
                        </div>
                        
                        <!-- Nav Links -->
                        <div class="nav flex-column nav-pills p-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            @if(auth()->user()->isAdmin())
                                <!-- Admin Navigation -->
                                <div class="px-3 mb-2 small text-uppercase text-white-50 fw-bold">ผู้ดูแลระบบ</div>
                                <a href="{{ route('admin.dashboard') }}" class="nav-link mb-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="fa-solid fa-gauge-high me-2"></i> แดชบอร์ดระบบ
                                </a>
                                <a href="{{ route('admin.users') }}" class="nav-link mb-3 {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                    <i class="fa-solid fa-users-gear me-2"></i> จัดการผู้ใช้
                                </a>
                                <hr class="border-secondary border-opacity-25 my-2">
                            @endif

                            <div class="px-3 mb-2 small text-uppercase text-white-50 fw-bold">ผู้ใช้งาน</div>
                            <a href="{{ route('user.dashboard') }}" class="nav-link mb-2 {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-table-columns me-2"></i> แดชบอร์ดของฉัน
                            </a>
                        </div>
                    </div>

                    <!-- User Profile & Logout -->
                    <div class="p-3 border-top border-secondary border-opacity-25">
                        <div class="d-flex align-items-center mb-3 px-2">
                            <div class="rounded-circle bg-light text-navy d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-weight: bold;">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="mb-0 text-white text-truncate">{{ auth()->user()->name }}</h6>
                                <span class="badge bg-secondary badge-sm" style="font-size: 10px;">
                                    {{ auth()->user()->isAdmin() ? 'ผู้ดูแลระบบ' : 'ผู้ใช้ทั่วไป' }}
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 btn-sm">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-md-9 col-lg-10 p-4" style="height: 100vh; overflow-y: auto;">
                    <!-- Top bar -->
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                        <h4 class="mb-0 text-navy-dark">@yield('page_title', 'แดชบอร์ด')</h4>
                        <div>
                            <span class="text-muted small">
                                <i class="fa-regular fa-clock me-1"></i> วันเวลาเข้าระบบ: {{ now()->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                    
                    @yield('content')
                </div>
            @else
                <!-- Guest View (Full width) -->
                <div class="col-12">
                    @yield('content')
                </div>
            @endauth
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery (Needed for Ajax modals) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert notifications -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: '{{ session('success') }}',
                confirmButtonColor: '#1d3557',
                confirmButtonText: 'ตกลง'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: '{{ session('error') }}',
                confirmButtonColor: '#e63946',
                confirmButtonText: 'ตกลง'
            });
        </script>
    @endif

    @yield('scripts')
</body>
</html>
