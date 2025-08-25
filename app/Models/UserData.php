<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserData extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_datas';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'address',
        'phone_number',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
