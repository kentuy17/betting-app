<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Fight;
use DateTimeInterface;

class BetHistory extends Model
{
    use HasFactory;
    protected $table = 'bet_history';
    protected $primaryKey = 'bethistory_no';
    protected $fillable = [
        'user_id',
        'fight_id',
        'fight_no',
        'status',
        'side',
        'percent',
        'betamount',
        'winamount',
        'current_points',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        // 'created_at' => 'datetime:M d, Y h:s A',
        'updated_at' => 'datetime:M d, Y h:i A',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('M d, Y H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function fight()
    {
        return $this->belongsTo(Fight::class, 'fight_no', 'fight_no');
    }

    /**
     * @var array
     */

    /**
     * createBetHistory
     * @param array $aParameter
     * @return mix
     */
    public function createBetHistory(array $aParameter)
    {
        return $this->create($aParameter);
    }

        /**
     * updateBetHistory
     * @param int $ibetHistoryID
     * @param array $aParameters
     * @return int
     */
    public function updateBetHistory(int $iBetHistoryID, array $aParameters) : int
    {
        return $this->where('bethistory_no', $iBetHistoryID)->update($aParameters);
    }

    /**
     * getBetHistoryUserID
     * @param string $iUserId
     * @return array
     */
    public function getBetHistoryUserID(string $iUserId) : array
    {
        return $this->where('user_id', $iUserId)->get()->toArray();
    }

}
