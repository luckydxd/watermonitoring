<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Branch.php
class Branch extends Model
{
    use HasFactory, HasUuids;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
