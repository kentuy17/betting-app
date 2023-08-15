<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpBan extends Model
{
    use HasFactory;

    protected $table = 'ip_bans';
    protected $fillable = [
        'id',
        'ip_address',
        'created_at',
        'updated_at',
    ];

}
