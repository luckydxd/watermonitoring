<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ComplaintApiController extends Controller
{

    public function index()
    {

        $data = Complaint::with([
            'user.userData',
            'user.branch'
        ])->latest();

        return DataTables::of($data)->make(true); // atau make(true)
    }


    public function show($id)
    {
        try {
            $complaint = Complaint::findOrFail($id);

            return response()->json([
                'complaint' => $complaint
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memuat data keluhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $data = $request->except('_token');

            $data['id'] = Str::uuid();
            $data['user_id'] = auth()->id();
            $data['status'] = 'pending';

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('complaints', 'public');
                $data['image'] = $path;
            }

            $complaint = Complaint::create($data);

            return response()->json([
                'message' => 'Keluhan berhasil ditambahkan!',
                'complaint' => $complaint
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat menyimpan keluhan: ' . $e->getMessage());

            return response()->json([
                'message' => 'Gagal menambahkan keluhan',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => '|string|max:255',
            'description' => '|string',
            'status' => 'required|in:pending,processed,resolved,rejected',
            'image' => 'nullable|image|max:2048'
        ]);

        $complaint = Complaint::findOrFail($id);
        $validated = $request->except(['_token', 'image']);

        if ($request->hasFile('image')) {
            if ($complaint->image) {
                Storage::disk('public')->delete($complaint->image);
            }
            $path = $request->file('image')->store('complaints', 'public');
            $validated['image'] = $path;
        }

        $complaint->update($validated);

        $recipient = $complaint->user;
        if ($recipient) {
            Notification::create([
                'id' => Str::uuid(),
                'user_id' => $recipient->id,
                'related_complaint_id' => $complaint->id,
                'title' => 'Status Keluhan Diperbarui',
                'content' => 'Status keluhan Anda "' . Str::limit($complaint->title, 50) . '" telah diubah menjadi ' . $complaint->status . '.',
                'type' => 'complaint_responded',

            ]);
        }

        return response()->json([
            'message' => 'Keluhan berhasil diperbarui',
            'data' => $complaint
        ]);
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $complaint = Complaint::findOrFail($id);

            if ($complaint->image) {
                Storage::disk('public')->delete($complaint->image);
            }

            $complaint->delete();

            DB::commit();

            return response()->json([
                'message' => 'Keluhan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus keluhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function process($id)
    {
        $complaint = Complaint::findOrFail($id);

        if ($complaint->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya keluhan dengan status pending yang bisa diproses'
            ], 400);
        }

        $complaint->update(['status' => 'processed']);

        $recipient = $complaint->user;
        if ($recipient) {
            Notification::create([
                'id' => Str::uuid(),
                'user_id' => $recipient->id,
                'related_complaint_id' => $complaint->id,
                'title' => 'Status Keluhan Diperbarui',
                'content' => 'Status keluhan Anda "' . Str::limit($complaint->title, 50) . '" Sedang Diproses. ',
                'type' => 'complaint_responded',

            ]);
        }

        return response()->json([
            'message' => 'Keluhan berhasil diproses',
            'data' => $complaint
        ]);
    }

    public function resolve($id)
    {
        try {
            $complaint = Complaint::findOrFail($id);

            if ($complaint->status !== 'processed') {
                return response()->json([
                    'message' => 'Hanya keluhan dengan status pending yang bisa diselesaikan'
                ], 422);
            }

            $complaint->update([
                'status' => 'resolved',
                'resolved_at' => now()
            ]);

            $recipient = $complaint->user;
            if ($recipient) {
                Notification::create([
                    'id' => Str::uuid(),
                    'user_id' => $recipient->id,
                    'related_complaint_id' => $complaint->id,
                    'title' => 'Status Keluhan Diperbarui',
                    'content' => 'Status keluhan Anda "' . Str::limit($complaint->title, 50) . '" selesai. ',
                    'type' => 'complaint_responded',

                ]);
            }

            return response()->json([
                'message' => 'Keluhan berhasil diselesaikan',
                'data' => $complaint
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Keluhan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyelesaikan keluhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function postComplaint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['title', 'description']);
            $data['id'] = Str::uuid();
            $data['user_id'] = auth()->id();
            $data['status'] = 'pending';

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('complaints', 'public');
                $data['image'] = $path;
            }

            $complaint = Complaint::create($data);

            DB::commit();

            $recipients = User::role(['admin'], 'web')->get();
            $complaintCreatorName = $complaint->user->userData->name ?? $complaint->user->name;

            foreach ($recipients as $recipient) {
                Notification::create([
                    'id' => Str::uuid(),
                    'user_id' => $recipient->id,
                    'related_complaint_id' => $complaint->id,
                    'title' => 'Keluhan Baru Diterima',
                    'content' => 'Keluhan baru "' . Str::limit($complaint->title, 50) . '" telah dibuat oleh ' . $complaintCreatorName . '.',
                    'type' => 'complaint_created',

                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Keluhan berhasil dikirim',
                'data' => [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'status' => $complaint->status,
                    'image_url' => $complaint->image ? asset('storage/' . $complaint->image) : null,
                    'created_at' => $complaint->created_at
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim keluhan',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getComplaint()
    {
        $complaints = Complaint::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'status' => $item->status,
                    'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                    'created_at' => $item->created_at->format('d M Y H:i')
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    public function getTechniciansByBranch(Branch $branch)
    {
        // Cek Otorisasi: Hanya admin yang boleh mengambil daftar teknisi
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk aksi ini.'], 403);
        }

        try {
            // Mengambil user dengan role 'teknisi' di cabang tertentu
            // dan memuat data personalnya (nama, dll) dari relasi userData
            $technicians = User::role('teknisi')
                ->where('branch_id', $branch->id)
                ->with('userData') // Eager load relasi userData
                ->get()
                ->map(function ($user) {
                    // Format ulang data agar lebih mudah digunakan di frontend
                    return [
                        'id' => $user->id,
                        'name' => $user->userData->name ?? $user->username, // Fallback ke username jika nama tidak ada
                    ];
                });

            return response()->json(['technicians' => $technicians]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data teknisi.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Membuat penugasan baru dari admin ke teknisi.
     * Aksi ini hanya boleh dilakukan oleh 'admin'.
     */
    public function assignTechnician(Request $request)
    {
        // Cek Otorisasi: Hanya admin yang boleh menugaskan teknisi
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk aksi ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'complaint_id' => 'required|exists:complaints,id',
            'technician_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $admin = auth()->user();
            $complaint = Complaint::findOrFail($request->complaint_id);
            $technician = User::findOrFail($request->technician_id);
            $customer = $complaint->user;

            // 1. Buat record penugasan
            $complaint->assignments()->create([
                'technician_id' => $technician->id,
                'admin_id'      => $admin->id,
                'status'        => 'in_progress',
                'notes'         => $request->notes,
            ]);

            // 2. Ubah status keluhan
            $complaint->update(['status' => 'processed']);

            // 3. Kirim notifikasi ke Teknisi dan Pelanggan
            Notification::create([
                'user_id' => $technician->id,
                'related_complaint_id' => $complaint->id,
                'title' => 'Tugas Baru Untuk Anda',
                'content' => 'Anda ditugaskan untuk menangani keluhan: "' . Str::limit($complaint->title, 50) . '".'
            ]);
            Notification::create([
                'user_id' => $customer->id,
                'related_complaint_id' => $complaint->id,
                'title' => 'Keluhan Anda Sedang Diproses',
                'content' => 'Teknisi kami telah ditugaskan untuk menangani keluhan Anda.',
                'type' => 'complaint_responded'
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Teknisi berhasil ditugaskan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menugaskan teknisi.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menyelesaikan penugasan (dilakukan oleh teknisi).
     * Aksi ini hanya boleh dilakukan oleh teknisi yang bersangkutan.
     */
}
