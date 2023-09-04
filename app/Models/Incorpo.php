<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'created_at',
        'updated_at',
    ];
}
