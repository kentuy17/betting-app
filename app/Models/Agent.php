<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $table = 'agents';
    protected $fillable = [
        'id',
        'rid',
        'user_id',
        'current_commission',
        'player_count',
        'is_master_agent',
        'type',
        'percent',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:M d, Y h:s A',
        'updated_at' => 'datetime:M d, Y h:s A',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agents_commission()
    {
        return $this->hasMany(AgentsCommission::class, 'agent_id', 'user_id');
    }

    public function referral()
    {
        return $this->hasMany(Referral::class, 'referrer_id', 'user_id');
    }
}
