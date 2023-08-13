<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class DerbyEvent extends Model
{
    use HasFactory;
    protected $table = 'derby_event';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'schedule_date',
        'schedule_time',
        'status',
        'added_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:M-d H:i:s',
        'updated_at' => 'datetime:M-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('M-d H:i:s');
    }

    public function fights()
    {
        $this->hasMany(Fight::class, 'event_id', 'id');
    }

    public function next()
    {
        return $this->where('id', '>', $this->id)->orderBy('id','asc')->first();

    }

    public  function previous()
    {
        return $this->where('id', '<', $this->id)->orderBy('id','desc')->first();
    }
}
