<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasFactory, HasUuids;

    // Nama tabel yang terkait dengan model
    protected $table = 'notifications';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'id',
        'user_id',
        'related_complaint_id',
        'related_response_id',
        'title',
        'content',
        'type',
        'is_read', // Tambahkan kolom ini untuk menandai notifikasi sudah dibaca atau belum
    ];

    // Cast attributes to native types (optional, but good practice)
    protected $casts = [
        'is_read' => 'boolean', // Pastikan is_read di-cast ke boolean
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan model Complaint (nullable)
    public function relatedComplaint()
    {
        return $this->belongsTo(Complaint::class, 'related_complaint_id');
    }

    // Relasi dengan model ComplaintResponse (nullable)
    public function relatedResponse()
    {
        return $this->belongsTo(ComplaintResponse::class, 'related_response_id');
    }

    /**
     * Scope a query to only include unread notifications.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include notifications for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
