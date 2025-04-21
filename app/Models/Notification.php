<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'related_complaint_id',
        'related_response_id',
        'title',
        'content',
        'type',
        'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'related_complaint_id');
    }

    public function response()
    {
        return $this->belongsTo(ComplaintResponse::class, 'related_response_id');
    }
}
