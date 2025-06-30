<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureBlockItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function feature_block()
    {
        return $this->belongsTo(FeatureBlock::class);
    }
}
