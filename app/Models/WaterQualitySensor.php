<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterQualitySensor extends Model
{
    use HasFactory;

    protected $table = 'water_quality_sensors';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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
