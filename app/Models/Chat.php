<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chat';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'message',
        'role_id',
        'sender',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'role_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:s A',
        'updated_at' => 'datetime:M d, Y h:s A',
    ];

}
