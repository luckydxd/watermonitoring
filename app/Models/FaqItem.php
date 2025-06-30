<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function faq_block()
    {
        return $this->belongsTo(FaqBlock::class);
    }
}
