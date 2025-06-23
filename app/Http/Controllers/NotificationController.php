<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User; // Pastikan User Model di-import jika digunakan secara langsung, meski sudah diakses via relasi Notification
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str; // Pastikan Str di-import untuk Str::limit

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function datatables()
    {
        $data = Notification::query()->where('user_id', auth()->id())->with(['user.userData', 'relatedComplaint', 'relatedResponse'])->latest();
        return DataTables::of($data)->make(true);
    }


    /**
     * Mengambil data notifikasi untuk DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();

        $query = Notification::query()
            ->with(['user.userData', 'relatedComplaint', 'relatedResponse']);

        if ($user->hasRole('user')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('is_read_filter') && $request->is_read_filter !== '') {
            $isRead = filter_var($request->is_read_filter, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_read', $isRead);
        }

        // Return DataTables response
        return DataTables::of($query)
            ->addColumn('no', function ($notification) {
                // `meta.row + 1` di frontend akan menangani penomoran.
                // Kolom ini hanya sebagai placeholder.
                return '';
            })
            ->addColumn('user_name', function ($notification) { // Hapus type hint `Notification $notification` untuk menghindari potensi masalah binding
                // Gunakan optional() untuk akses relasi yang aman
                return optional(optional($notification->user)->userData)->name ?? 'N/A';
            })
            ->addColumn('type_formatted', function ($notification) { // Hapus type hint `Notification $notification`
                switch ($notification->type) {
                    case 'complaint_created':
                        return '<span class="badge bg-label-danger">Keluhan Baru</span>';
                    case 'complaint_responded':
                        return '<span class="badge bg-label-info">Respon Keluhan</span>';
                    case 'general':
                        return '<span class="badge bg-label-primary">Umum</span>';
                    default:
                        return '<span class="badge bg-label-secondary">' . Str::title(str_replace('_', ' ', $notification->type)) . '</span>';
                }
            })
            ->addColumn('content_short', function ($notification) { // Hapus type hint `Notification $notification`
                return Str::limit($notification->content, 70, '...');
            })
            ->addColumn('is_read_formatted', function ($notification) { // Hapus type hint `Notification $notification`
                $badgeClass = $notification->is_read ? "bg-label-success" : "bg-label-warning";
                $statusText = $notification->is_read ? "Sudah Dibaca" : "Belum Dibaca";
                return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>'; // Gunakan string concatenation
            })
            ->addColumn('actions', function ($notification) { // Hapus type hint `Notification $notification`
                $buttons = '';

                if (!$notification->is_read) {
                    $buttons .= '
                        <button class="btn btn-icon btn-label-success mark-as-read-btn" data-id="' . $notification->id . '" title="Tandai Sudah Dibaca">
                            <i class="ti ti-check"></i>
                        </button>
                    ';
                }

                $viewLink = '#';
                if ($notification->type === 'complaint_created' && $notification->relatedComplaint) {
                    $viewLink = route('complaints.show', $notification->relatedComplaint->id);
                } elseif ($notification->type === 'complaint_responded' && $notification->relatedResponse) {
                    $viewLink = route('complaint_responses.show', $notification->relatedResponse->id);
                }

                $buttons .= '
                    <a href="' . $viewLink . '" class="btn btn-icon btn-label-info view-notification-btn" title="Lihat Detail">
                        <i class="ti ti-eye"></i>
                    </a>
                ';

                $buttons .= '
                    <button class="btn btn-icon btn-label-danger delete-notification-btn" data-id="' . $notification->id . '" title="Hapus">
                        <i class="ti ti-trash"></i>
                    </button>
                ';

                return '<div class="d-inline-flex gap-2">' . $buttons . '</div>';
            })
            ->rawColumns(['type_formatted', 'is_read_formatted', 'actions'])
            ->make(true);
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::forUser(Auth::id())->findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notifikasi berhasil ditandai sudah dibaca.']);
    }

    /**
     * Menandai semua notifikasi pengguna sebagai sudah dibaca.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi berhasil ditandai sudah dibaca.']);
    }

    /**
     * Menghapus notifikasi.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $notification = Notification::forUser(Auth::id())->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notifikasi berhasil dihapus.']);
    }

    /**
     * Mengambil jumlah notifikasi belum dibaca untuk navbar dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->notifications()->unread()->count();
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Mengambil notifikasi terbaru untuk navbar dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestNotifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderByDesc('created_at')
            ->limit(5) // Ambil 5 notifikasi terbaru
            ->get();
        return response()->json(['notifications' => $notifications]);
    }
}
