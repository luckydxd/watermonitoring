<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller

{
    public function getNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->simplePaginate(15);

        return response()->json($notifications);
    }

    public function getUnreadCount(Request $request)
    {
        $count = $request->user()->notifications()->unread()->count();
        return response()->json(['unread_count' => $count]);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }


    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }

    public function destroy(string $id) // Gunakan type hint 'string' karena ID adalah UUID
    {
        try {
            // Ambil pengguna yang sedang login melalui guard 'api' (JWT)
            $user = Auth::guard('api')->user();

            // Cari notifikasi berdasarkan ID dan pastikan itu milik pengguna yang sedang login.
            // Gunakan `forUser()` scope yang sudah Anda definisikan di model Notification.
            $notification = Notification::forUser($user->id)->findOrFail($id);

            // Hapus notifikasi
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Jika notifikasi tidak ditemukan atau bukan milik user ini
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.'
            ], 404);
        } catch (\Exception $e) {
            // Tangani error lain yang mungkin terjadi
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
