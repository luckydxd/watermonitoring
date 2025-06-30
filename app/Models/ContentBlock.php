<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function blockable()
    {
        return $this->morphTo();
    }
}
