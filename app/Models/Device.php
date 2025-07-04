<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;

class Device extends Model implements Authenticatable
{
    use HasFactory, AuthenticatableTrait;

    protected $table = 'devices';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'device_type_id',
        'unique_id',
        'api_key',
        'status',
        'last_seen_at',
    ];

    protected $hidden = [
        'api_key',
    ];

    // app/Models/Device.php
    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function deviceAssignments()
    {
        return $this->hasMany(DeviceAssignment::class);
    }


    public function consumptionLogs()
    {
        return $this->hasMany(WaterConsumptionLog::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'device_assignments')
            ->using(DeviceAssignment::class)
            ->withPivot(['created_at', 'is_active', 'notes']); // Pastikan 'created_at' di sini juga
    }

    public function flowPressureSensors()
    {
        return $this->hasMany(FlowPressureSensor::class);
    }

    /**
     * Get the water quality readings for the device.
     */
    public function waterQualitySensors()
    {
        return $this->hasMany(WaterQualitySensor::class);
    }
}
