<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WaterQualitySensor extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    protected $table = 'water_quality_sensors';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'device_id',
        'water_level',
        'turbidity',
        'measured_at',
    ];

    protected $casts = [
        'water_level' => 'float',
        'turbidity' => 'float',
        'measured_at' => 'datetime',
    ];


    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
