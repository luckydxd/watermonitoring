<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowPressureSensor extends Model
{
    use HasFactory;

    protected $table = 'flow_pressure_sensors';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'device_id',
        'flow_rate',
        'pressure',
        'measured_at',
    ];

    protected $casts = [
        'flow_rate' => 'float',
        'pressure' => 'float',
        'measured_at' => 'datetime',
    ];


    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
