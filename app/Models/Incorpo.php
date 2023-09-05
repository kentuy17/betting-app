<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incorpo extends Model
{
    use HasFactory;

    protected $table = 'incorpos';

    protected $fillable = [
        'id',
        'user_id',
        'master_agent',
        'player_count',
        'bracket',
        'default_pass',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:s A',
        'updated_at' => 'datetime:M d, Y h:s A',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent_commission()
    {
        return $this->hasOne(AgentCommission::class, 'user_id', 'user_id');
    }
}
