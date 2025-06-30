<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasFileDeletion;

class DownloadLink extends Model
{
    use HasFactory, HasFileDeletion;
    protected $guarded = [];
    protected $fileFieldsToDelete = ['icon_path'];


    public function download_block()
    {
        return $this->belongsTo(DownloadBlock::class);
    }
}
