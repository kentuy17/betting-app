<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promos';

    protected $fillable = [
        'id',
        'user_id',
        'code',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
