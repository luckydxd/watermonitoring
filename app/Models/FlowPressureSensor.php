<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;



class FlowPressureSensor extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    protected $table = 'flow_pressure_sensors';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'device_id',
        'flow_rate',
        'pressure',
        'volume',
        'measured_at',
    ];

    protected $casts = [
        'flow_rate' => 'float',
        'pressure' => 'float',
        'volume' => 'float',
        'measured_at' => 'datetime',
    ];


    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
