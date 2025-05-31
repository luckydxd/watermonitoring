<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'description',
        'image',
        'status',
        'timestamp',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/images/complaints/' . $this->image_path);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(ComplaintResponse::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_complaint_id');
    }
}
