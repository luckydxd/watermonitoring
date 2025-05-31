<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterConsumptionLog extends Model
{
    use HasFactory;

    protected $table = 'water_consumption_logs';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'date',
        'total_consumption',
    ];

    protected $casts = [
        'date' => 'date',
        'total_consumption' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
