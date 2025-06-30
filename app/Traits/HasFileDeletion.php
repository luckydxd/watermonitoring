<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasFileDeletion
{
    protected static function bootHasFileDeletion()
    {
        static::deleting(function ($model) {
            if (property_exists($model, 'fileFieldsToDelete')) {
                foreach ($model->fileFieldsToDelete as $field) {
                    $filePath = $model->{$field};

                    if ($filePath) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }
        });
    }
}
