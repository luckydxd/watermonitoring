<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceAssignment extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'device_id',
        'assignment_date',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'assignment_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    // Relasi yang benar
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
