<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class CommissionHistory extends Model
{
    use HasFactory;

    protected $table = 'commission_history';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'user_id',
        'points_converted',
        'current_points',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('M-d-y H:i:s');
    }
}
