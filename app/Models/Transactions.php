<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transactions extends Model
{
    use HasFactory;
    protected $table = 'trasnsactions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'user_id',
        'amount',
        'mobile_number',
        'status',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
