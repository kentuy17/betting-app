<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ShareHolder extends Model
{
    use HasFactory;

    protected $table = 'share_holders';

    protected $fillable = [
        'id',
        'user_id',
        'percentage',
        'role_description',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
