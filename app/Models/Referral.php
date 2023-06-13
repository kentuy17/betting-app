<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Referral extends Model
{
    use HasFactory;

    protected $table = 'hacking';
    protected $fillable = [
        'id',
        'rid',
        'referrer_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'referrer_id');
    }
}
