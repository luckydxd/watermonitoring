<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor_datas';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'device_id',
        'pressure',
        'flow_rate',
        'water_level',
        'turbidity',
        'timestamp',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
