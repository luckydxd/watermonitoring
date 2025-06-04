<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorActivity extends Model
{
    protected $fillable = [
        'date',
        'visitors',
        'contact_clicks',
        'login_clicks',
        'download_clicks'
    ];

    protected $dates = ['date'];
}
