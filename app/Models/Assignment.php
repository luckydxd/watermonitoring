<?php

namespace App\Models;

use App\Enums\AssignmentStatus; // <-- Import Enum yang baru dibuat
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory, HasUuids; // <-- Tambahkan HasUuids karena Anda menggunakan UUID

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'complaint_id',
        'technician_id',
        'admin_id',
        'status',
        'notes',
        'completion_notes',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Casting status ke Enum membuat kode Anda lebih aman dan ekspresif
        'status' => AssignmentStatus::class,

        // Casting completed_at ke objek Carbon untuk kemudahan manipulasi tanggal
        'completed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships / Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Mendefinisikan bahwa sebuah penugasan PASTI dimiliki oleh satu keluhan.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    public function assignable()
    {
        return $this->morphTo();
    }
    /**
     * Mendefinisikan bahwa sebuah penugasan PASTI dimiliki oleh satu teknisi.
     * Kita menunjuk ke model User.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Mendefinisikan bahwa sebuah penugasan PASTI dibuat oleh satu admin.
     * Kita juga menunjuk ke model User.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
