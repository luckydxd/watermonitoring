<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFileDeletion;

class FeatureBlock extends Model
{
    use HasFactory, HasFileDeletion;
    protected $guarded = [];

    protected $fileFieldsToDelete = ['image_path'];

    public function content_block()
    {
        return $this->morphOne(ContentBlock::class, 'blockable');
    }

    public function items()
    {
        return $this->hasMany(FeatureBlockItem::class);
    }

    // Method boot untuk memicu penghapusan pada relasi 'items'
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($featureBlock) {
            // Saat FeatureBlock dihapus, loop semua itemnya dan hapus satu per satu
            $featureBlock->items()->each(function ($item) {
                $item->delete();
            });
        });
    }
}
