<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterConsumptionLog extends Model
{
    use HasFactory;

    protected $table = 'water_consumption_logs';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'date',
        'total_consumption',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
