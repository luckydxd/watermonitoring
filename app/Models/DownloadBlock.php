<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFileDeletion;

class DownloadBlock extends Model
{
    use HasFactory, HasFileDeletion;
    protected $guarded = [];

    protected $fileFieldsToDelete = ['mockup_image_path'];

    public function content_block()
    {
        return $this->morphOne(ContentBlock::class, 'blockable');
    }

    public function links()
    {
        return $this->hasMany(DownloadLink::class);
    }


    // Method boot untuk memicu penghapusan pada relasi 'links'
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($downloadBlock) {
            $downloadBlock->links()->each(function ($link) {
                $link->delete();
            });
        });
    }
}
