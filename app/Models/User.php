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

    public function devices()
    {
        return $this->hasManyThrough(
            Device::class,
            DeviceAssignment::class,
            'user_id',
            'id',
            'id',
            'device_id'
        )->where('device_assignments.is_active', true);
    }
    //     public function devices()
    // {
    //     return $this->belongsToMany(Device::class, 'device_assignments')
    //                 ->using(DeviceAssignment::class)
    //                 ->withPivot(['created_at', 'is_active', 'notes']);
    // }

    public function userData()
    {
        return $this->hasOne(UserData::class);
    }

    public function waterConsumptionLogs()
    {
        return $this->hasMany(WaterConsumptionLog::class, 'user_id');
    }


    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

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
