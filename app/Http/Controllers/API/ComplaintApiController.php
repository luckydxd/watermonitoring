<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class ComplaintApiController extends Controller
{
    public function index()
    {
        $data = Complaint::query()->with(['user.userData'])->latest();
        return DataTables::of($data)->make(true);
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:pending,processing,resolved',
            'location' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except(['_token', '_method', 'image']);
            $data['id'] = (string) Str::uuid();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'complaints/' . time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('public/complaints', $filename);
                $data['image'] = 'complaints/' . $filename;
            }

            $complaint = Complaint::create($data);


            DB::commit();

            return response()->json([
                'message' => 'Keluhan berhasil ditambahkan!',
                'complaint' => $complaint,
                'image_url' => $complaint->image ? asset('storage/' . $complaint->image) : null
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();


            return response()->json([
                'message' => 'Gagal menambahkan keluhan',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan server'
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

        if ($request->hasFile('image')) {
            if ($complaint->image) {
                Storage::delete('public/' . $complaint->image);
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
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $data = $request->only(['title', 'description', 'location']);
            $data['id'] = Str::uuid();
            $data['user_id'] = $user->id;
            $data['status'] = 'pending';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'complaints/' . time() . '_' . Str::slug($request->title) . '.' . $file->extension();

                $path = $file->storeAs('public/complaints', $filename);
                $data['image'] = 'complaints/' . $filename;
            }

            $complaint = Complaint::create($data);

            DB::commit();

            $recipients = User::role(['admin', 'teknisi'], 'web')->get();
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
}
