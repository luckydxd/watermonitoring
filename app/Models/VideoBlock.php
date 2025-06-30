<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFileDeletion;


class VideoBlock extends Model
{
    use HasFactory, HasFileDeletion;
    protected $guarded = [];

    protected $fileFieldsToDelete = ['video_path', 'thumbnail_path'];

    public function content_block()
    {
        return $this->morphOne(ContentBlock::class, 'blockable');
    }
}
