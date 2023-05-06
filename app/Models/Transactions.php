<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'mobile_number',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:s A',
        'updated_at' => 'datetime:M d, Y h:s A',
    ];

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
}
