<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_app',
        'desc',
        'logo',
        'secondary_logo',
        'app_mockup',
        'address',
        'email',
        'phone',
        'whatsapp',
        'instagram',
        'youtube',
        'gmap_coordinat'
    ];
}
