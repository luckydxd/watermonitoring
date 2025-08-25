<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Complaint;
use App\Models\InstallationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

// app/Http/Controllers/Api/AssignmentApiController.php
class AssignmentApiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Assignment::with([
            // Eager load relasi polymorphic
            'assignable',
            // Eager load relasi teknisi dan data personalnya
            'technician.userData',
            // Eager load relasi admin dan data personalnya
            'admin.userData',
            'assignable.user.userData',
        ]);

        if ($user->hasRole('teknisi')) {
            $query->where('technician_id', $user->id);
        } elseif ($user->hasRole('admin')) {
            // Admin bisa melihat semua tugas di cabangnya
            $query->whereHas('technician', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        // Ini adalah bagian dari Yajra DataTables
        return DataTables::of($query)->make(true);
    }

    public function completeAssignment(Request $request, Assignment $assignment)
    {
        $user = Auth::user();

        // Otorisasi: Pastikan ini adalah tugas milik teknisi yang login
        if (!$user->hasRole('teknisi') || $user->id !== $assignment->technician_id) {
            return response()->json(['message' => 'Akses ditolak. Ini bukan tugas Anda.'], 403);
        }

        // --- AWAL PERBAIKAN ---
        // Muat ulang data assignment beserta relasi yang dibutuhkan secara eksplisit.
        // Ini akan memastikan $assignment->assignable dan $assignment->assignable->user tidak null.
        $assignment->load('assignable.user.userData');
        // --- AKHIR PERBAIKAN ---

        $request->validate([
            'completion_notes' => 'required|string',
            'completion_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // 1. Ambil objek yang ditugaskan (sekarang dijamin ada)
            $assignable = $assignment->assignable;

            if (!$assignable) {
                throw new \Exception('Relasi tugas tidak ditemukan.');
            }

            // 2. Dapatkan data pelanggan (customer) dari relasi (sekarang dijamin ada)
            $customer = $assignable->user;

            if (!$customer) {
                throw new \Exception('Data pelanggan tidak ditemukan untuk tugas ini.');
            }

            // 3. Update status assignment
            $assignment->update([
                'status' => 'completed',
                'completion_notes' => $request->completion_notes,
                // 'completion_image' => $path_jika_ada,
                'completed_at' => now(),
            ]);

            // 4. Update status tugas aslinya
            $finalStatus = ($assignable instanceof \App\Models\Complaint) ? 'resolved' : 'completed';
            $assignable->update(['status' => $finalStatus]);

            // 5. Kirim notifikasi menggunakan data customer yang sudah didapat
            Notification::create([
                'user_id' => $customer->id,
                'related_complaint_id' => ($assignable instanceof \App\Models\Complaint) ? $assignable->id : null,
                'title' => 'Tugas Telah Selesai',
                'content' => 'Tugas "' . ($assignable->title ?? 'Pemasangan Baru') . '" telah berhasil ditangani.',
                'type' => 'general'
            ]);

            $adminRecipients = User::role('admin')->where('branch_id', $customer->branch_id)->get();
            foreach ($adminRecipients as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'related_complaint_id' => ($assignable instanceof \App\Models\Complaint) ? $assignable->id : null,
                    'title' => 'Laporan Tugas Selesai',
                    'content' => 'Tugas untuk pelanggan ' . ($customer->userData->name ?? $customer->name) . ' telah diselesaikan oleh teknisi.',
                    'type' => 'general'
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tugas berhasil diselesaikan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyelesaikan tugas.', 'error' => $e->getMessage()], 500);
        }
    }
}
