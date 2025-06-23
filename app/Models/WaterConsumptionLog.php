<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WaterConsumptionLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'water_consumption_logs';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'total_consumption',
    ];

    protected $casts = [
        'total_consumption' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // app/Models/WaterConsumptionLog.php

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeFilterByMonth($query, $month)
    {
        if ($month) {
            return $query->whereMonth('date', $month);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeFilterByYear($query, $year)
    {
        if ($year) {
            return $query->whereYear('date', $year);
        }
        return $query;
    }
}
