<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Fight;

class Bet extends Model
{
    use HasFactory;
    protected $table = 'bets';
    protected $primaryKey = 'bet_no';
    protected $fillable = [
        'bet_no',
        'fight_id',
        'fight_no',
        'user_id',
        'amount',
        'side',
        'status',
        'win_amount',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:s A',
        'updated_at' => 'datetime:M d, Y h:s A',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function fight()
    {
        return $this->belongsTo(Fight::class, 'fight_id', 'id');
    }

    public function referral()
    {
        return $this->hasOne(Referral::class, 'user_id', 'user_id');
    }
}
