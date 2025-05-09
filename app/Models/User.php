<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


// Jika menggunakan Spatie Laravel Permission
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function deviceAssignments()
    {
        return $this->hasMany(DeviceAssignment::class);
    }

    // Relasi ke devices
    public function devices()
    {
        return $this->belongsToMany(Device::class, 'device_assignments')
            ->using(DeviceAssignment::class)
            ->withPivot(['assignment_date', 'is_active', 'notes']);
    }

    // Relasi ke tabel user_datas
    public function userData()
    {
        return $this->hasOne(UserData::class);
    }


    // Relasi ke complaints
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    // Relasi ke notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
