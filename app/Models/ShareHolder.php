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
        'current_commission',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/y h:s',
        'updated_at' => 'datetime:M d, Y h:s A',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
