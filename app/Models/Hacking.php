<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hacking extends Model
{
    use HasFactory;
    
    protected $table = 'hacking';
    protected $fillable = [
        'id',
        'user_id',
        'request',
        'violation',
        'created_at',
        'updated_at',
    ];

}
