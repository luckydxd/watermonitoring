<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'device_type_id',
        'unique_id',
        'status',
        'latitude',
        'longitude',
    ];

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function deviceAssignments()
    {
        return $this->hasMany(DeviceAssignment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'device_assignments')
            ->using(DeviceAssignment::class)
            ->withPivot(['assignment_date', 'is_active', 'notes']);
    }

    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }
}
