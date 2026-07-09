@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน')
@section('page_title', 'ระบบจัดการข้อมูลผู้ใช้งาน')

@section('content')
<div class="card card-premium shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold text-navy"><i class="fa-solid fa-users-gear me-2"></i>รายชื่อผู้ใช้งานในระบบ</span>
        <button class="btn btn-navy btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa-solid fa-user-plus me-1"></i> เพิ่มผู้ใช้งานใหม่
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 table-premium">
                <thead>
                    <tr>
                        <th class="ps-4">ชื่อ-นามสกุล</th>
                        <th>อีเมล</th>
                        <th>บทบาท</th>
                        <th>วันที่เพิ่ม</th>
                        <th class="pe-4 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4 font-weight-semibold">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-navy-light text-navy d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-weight: bold;">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="badge bg-danger badge-status"><i class="fa-solid fa-user-shield me-1"></i> ผู้ดูแลระบบ</span>
                                @else
                                    <span class="badge bg-secondary badge-status"><i class="fa-solid fa-user me-1"></i> ผู้ใช้ทั่วไป</span>
                                @endif
                            </td>
                            <td class="small">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="pe-4 text-center">
                                <div class="btn-group btn-group-sm">
                                    <!-- Edit Button (trigger Modal) -->
                                    <button class="btn btn-outline-navy btn-edit-user" 
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUserModal">
                                        <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                    </button>

                                    <!-- Delete Button (triggers SweetAlert) -->
                                    @if($user->id !== auth()->id())
                                        <button class="btn btn-outline-danger btn-delete-user" data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                                            <i class="fa-solid fa-trash-can"></i> ลบ
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" id="delete-form-{{ $user->id }}" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        <button class="btn btn-outline-secondary disabled" title="ไม่สามารถลบตัวเองได้">
                                            <i class="fa-solid fa-ban"></i> ลบ
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title" id="addUserModalLabel"><i class="fa-solid fa-user-plus me-2"></i>เพิ่มบัญชีผู้ใช้งานใหม่</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add-name" class="form-label fw-semibold text-navy">ชื่อ-นามสกุล</label>
                        <input type="text" name="name" id="add-name" class="form-control" placeholder="สมชาย มุ่งมั่น" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-email" class="form-label fw-semibold text-navy">อีเมล</label>
                        <input type="email" name="email" id="add-email" class="form-control" placeholder="user@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-password" class="form-label fw-semibold text-navy">รหัสผ่านเริ่มต้น</label>
                        <input type="password" name="password" id="add-password" class="form-control" placeholder="ไม่ต่ำกว่า 8 ตัวอักษร" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-role" class="form-label fw-semibold text-navy">บทบาทในระบบ</label>
                        <select name="role" id="add-role" class="form-select" required>
                            <option value="user">ผู้ใช้ทั่วไป (User)</option>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-navy">บันทึกข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="POST" id="edit-user-form">
            @csrf
            @method('PUT')
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title" id="editUserModalLabel"><i class="fa-solid fa-user-pen me-2"></i>แก้ไขข้อมูลผู้ใช้งาน</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label fw-semibold text-navy">ชื่อ-นามสกุล</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label fw-semibold text-navy">อีเมล</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-password" class="form-label fw-semibold text-navy">เปลี่ยนรหัสผ่าน (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
                        <input type="password" name="password" id="edit-password" class="form-control" placeholder="ปล่อยว่างไว้เพื่อใช้รหัสผ่านเดิม">
                    </div>
                    <div class="mb-3">
                        <label for="edit-role" class="form-label fw-semibold text-navy">บทบาทในระบบ</label>
                        <select name="role" id="edit-role" class="form-select" required>
                            <option value="user">ผู้ใช้ทั่วไป (User)</option>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-navy">ปรับปรุงข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle Edit Button Click to populate fields dynamically
        $('.btn-edit-user').on('click', function() {
            const btn = $(this);
            const id = btn.data('id');
            const name = btn.data('name');
            const email = btn.data('email');
            const role = btn.data('role');

            // Set Form action endpoint
            $('#edit-user-form').attr('action', `/admin/users/${id}`);

            // Set Field values
            $('#edit-name').val(name);
            $('#edit-email').val(email);
            $('#edit-role').val(role);
            $('#edit-password').val(''); // Clear password field
        });

        // Handle Delete Button Click with SweetAlert Confirmation
        $('.btn-delete-user').on('click', function() {
            const btn = $(this);
            const id = btn.data('id');
            const name = btn.data('name');

            Swal.fire({
                title: 'ยืนยันการลบผู้ใช้?',
                text: `คุณต้องการลบข้อมูลผู้ใช้งาน "${name}" ออกจากระบบใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนคืนได้!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ต้องการลบ!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the hidden delete form
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        });
    });
</script>
@endsection
