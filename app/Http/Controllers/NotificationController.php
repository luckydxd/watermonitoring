<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

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

        return DataTables::of($query)
            ->addColumn('no', function ($notification) {
                return '';
            })
            ->addColumn('user_name', function ($notification) {
                return optional(optional($notification->user)->userData)->name ?? 'N/A';
            })
            ->addColumn('type_formatted', function ($notification) {
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
            ->addColumn('content_short', function ($notification) {
                return Str::limit($notification->content, 70, '...');
            })
            ->addColumn('is_read_formatted', function ($notification) {
                $badgeClass = $notification->is_read ? "bg-label-success" : "bg-label-warning";
                $statusText = $notification->is_read ? "Sudah Dibaca" : "Belum Dibaca";
                return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->addColumn('actions', function ($notification) {
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

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::forUser(Auth::id())->findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notifikasi berhasil ditandai sudah dibaca.']);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi berhasil ditandai sudah dibaca.']);
    }

    public function destroy($id)
    {
        $notification = Notification::forUser(Auth::id())->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notifikasi berhasil dihapus.']);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->notifications()->unread()->count();
        return response()->json(['unread_count' => $count]);
    }

    public function getLatestNotifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        return response()->json(['notifications' => $notifications]);
    }
}
