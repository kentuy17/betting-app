<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use DateTimeInterface;

class Transactions extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'user_id',
        'amount',
        'action',
        'processedBy',
        'receipt_name',
        'mobile_number',
        'status',
        'reference_code',
        'filename',
        'note',
        'created_at',
        'updated_at',
        'outlet',
        'completed_at'
    ];

    protected $casts = [
        // 'created_at' => 'datetime:m-d-y H:i:s',
        // 'updated_at' => 'datetime:M d, Y h:i A',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('m-d-y H:i:s');
    }

    /**
     * createTransaction
     * @param array $aParameter
     * @return mix
     */
    public function createTransaction(array $aParameter)
    {
        return $this->create($aParameter);
    }

    /**
     * createTransaction
     * @param array $aParameter
     * @return mix
     */
    public function updateStatus(int $transID, array $aParameters)
    {
        return $this->where('id', $transID)->update($aParameters);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }
}
