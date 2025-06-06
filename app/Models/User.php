<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;
    /**
     * @method bool hasRole(string|array $roles)
     */
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

    public function waterConsumptionLogs()
    {
        return $this->hasMany(WaterConsumptionLog::class, 'user_id');
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
