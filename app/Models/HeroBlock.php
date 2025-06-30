<?php

namespace App\Models;

use App\Traits\HasFileDeletion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroBlock extends Model
{
    use HasFactory, HasFileDeletion;
    protected $guarded = [];

    protected $fileFieldsToDelete = ['image_path'];


    public function content_block()
    {
        return $this->morphOne(ContentBlock::class, 'blockable');
    }
}
