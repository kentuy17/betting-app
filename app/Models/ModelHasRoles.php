<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Roles;
use App\Models\User;

class ModelHasRoles extends Model
{
    use HasFactory;
    protected $table = 'model_has_roles';

    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
        'updated_at',
        'created_at'
    ];

    public function roles()
    {
        return $this->belongsTo(Roles::class, 'role_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'model_id', 'id');
    }
}

