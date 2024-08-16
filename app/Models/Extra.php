<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    use HasFactory;

    protected $table = 'extras';

    protected $fillable = [
        'id',
        'fight_no',
        'event_id',
        'user_id',
        'amount',
        'created_at',
        'updated_at',
    ];
}
