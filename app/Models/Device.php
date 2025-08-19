<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Carbon\Carbon;
// use App\Models\DeviceAssignment;

class Device extends Model implements Authenticatable
{
    use HasFactory, AuthenticatableTrait;

    protected $table = 'devices';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $appends = ['operational_status'];
    const OFFLINE_THRESHOLD_MINUTES = 15;



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
    public function activeAssignment()
    {
        return $this->hasOne(DeviceAssignment::class)->where('is_active', true);
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



    /**
     * Accessor untuk mendapatkan status operasional gabungan dari perangkat.
     * Ini akan menciptakan atribut virtual 'operational_status'.
     *
     * @return array
     */


    public function getOperationalStatusAttribute(): array
    {
        // Cek kondisi untuk status "Active"
        // HARUS memenuhi SEMUA kriteria:
        // 1. Status di DB adalah 'active'
        // 2. Sudah terpasang/ditugaskan (punya activeAssignment)
        // 3. Pernah mengirim data (last_seen_at tidak null)
        // 4. Kiriman datanya kurang dari 15 menit yang lalu
        if (
            $this->status === 'active' &&
            $this->activeAssignment &&
            $this->last_seen_at &&
            Carbon::parse($this->last_seen_at)->diffInMinutes(now()) < 15
        ) {
            return [
                'status_text' => 'Active',
                'badge_class' => 'bg-label-success'
            ];
        }

        // Jika salah satu kondisi di atas tidak terpenuhi,
        // maka statusnya dianggap "Inactive".
        return [
            'status_text' => 'Inactive',
            'badge_class' => 'bg-label-danger'
        ];
    }
}
