<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InstallationRequest extends Model
{
    use HasFactory, HasUuids;

    public function assignments()
    {
        return $this->morphMany(Assignment::class, 'assignable');
    }

    protected static function booted(): void
    {
        static::deleting(function (InstallationRequest $installationRequest) {
            // Hapus semua assignment yang terkait dengan permintaan pemasangan ini.
            $installationRequest->assignments()->delete();
        });
    }
}
