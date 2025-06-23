<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationApiController extends Controller

{
    /**
     * Mengambil daftar notifikasi untuk user yang sedang login dengan format JSON sederhana dan paginasi.
     * Cocok untuk infinite scrolling di mobile.
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->simplePaginate(15);

        return response()->json($notifications);
    }

    /**
     * Mengambil jumlah notifikasi yang belum dibaca.
     */
    public function getUnreadCount(Request $request)
    {
        $count = $request->user()->notifications()->unread()->count();
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        // Pastikan user hanya bisa menandai notifikasinya sendiri
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->markAsRead(); // Eloquent Notifications trait method

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Menandai semua notifikasi yang belum dibaca sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
