<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

use App\Models\User;
use App\Models\Agent;

class AgentCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'agent_id',
        'user_id',
        'commission',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('M-d-y H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
