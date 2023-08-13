<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Referral extends Model
{
    use HasFactory;

    protected $table = 'referrals';
    protected $fillable = [
        'id',
        'rid',
        'referrer_id',
        'user_id',
        'promo_done',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:s A',
        'updated_at' => 'datetime:M d, Y h:s A',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bet()
    {
        return $this->hasMany(Bet::class, 'user_id', 'user_id');
    }
}
