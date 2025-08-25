<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'status'
    ];

    protected static function booted(): void
    {
        // 'deleting' adalah sebuah event yang terjadi TEPAT SEBELUM
        // sebuah record akan dihapus dari database.
        static::deleting(function (Complaint $complaint) {
            // Untuk setiap keluhan yang akan dihapus,
            // cari semua 'assignments'-nya dan hapus juga.
            $complaint->assignments()->delete();
        });
    }

    public function assignments()
    {
        return $this->morphMany(Assignment::class, 'assignable');
    }


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

    public function branch()
    {
        return $this->hasOneThrough(
            Branch::class,
            User::class,
            'id',
            'id',
            'user_id',
            'branch_id'
        );
    }
}
