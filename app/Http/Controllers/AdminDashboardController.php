<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard with all documents.
     */
    public function index(Request $request)
    {
        // Calculate system statistics
        $stats = [
            'total' => Document::count(),
            'pending' => Document::where('status', 'pending')->count(),
            'processing' => Document::where('status', 'processing')->count(),
            'completed' => Document::where('status', 'completed')->count(),
            'failed' => Document::where('status', 'failed')->count(),
            'found' => Document::where('result_status', 'found')->count(),
            'not_found' => Document::where('result_status', 'not_found')->count(),
            'users_count' => User::count(),
        ];

        // Retrieve all documents with users
        $query = Document::with('user');

        // Optional search or filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('result_status')) {
            $query->where('result_status', $request->result_status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.dashboard', compact('documents', 'stats'));
    }

    /**
     * Fetch document details as JSON for Ajax Modal popup.
     */
    public function getDocumentDetails($id)
    {
        $document = Document::with('user')->findOrFail($id);
        
        return response()->json([
            'id' => $document->id,
            'title' => $document->title,
            'user' => $document->user->name,
            'email' => $document->user->email,
            'target_text' => $document->target_text,
            'status' => $document->status,
            'result_status' => $document->result_status,
            'summary' => $document->summary ?? 'ไม่มีสรุปข้อมูล',
            'details' => $document->details ?? 'ไม่มีรายละเอียด',
            'error_message' => $document->error_message,
            'created_at' => $document->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $document->updated_at->format('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Display user management screen.
     */
    public function usersList()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    /**
     * Store new user.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้ใช้',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้วในระบบ',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'เพิ่มผู้ใช้งานสำเร็จ');
    }

    /**
     * Update existing user.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้ใช้',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้วในระบบ',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'ปรับปรุงข้อมูลผู้ใช้งานสำเร็จ');
    }

    /**
     * Delete user from system.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')->with('error', 'ไม่สามารถลบตัวเองได้');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'ลบผู้ใช้งานสำเร็จแล้ว');
    }
}
