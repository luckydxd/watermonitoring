<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqBlock extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function content_block()
    {
        return $this->morphOne(ContentBlock::class, 'blockable');
    }

    public function items()
    {
        return $this->hasMany(FaqItem::class);
    }
}
