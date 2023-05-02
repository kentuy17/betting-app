<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

        /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone_no',
        'password',
        'email',
        'points'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function bet()
    {
        return $this->hasMany(Bet::class, 'user_id', 'id');
    }

    /**
     * getProfileByUserID
     * @param string $iUserId
     * @return array
     */
    public function getProfileByUserID(string $iUserId) : array
    {
        return $this->where('id', $iUserId)->get()->toArray();
    }

        /**
     * updateProfile
     * @param int $userID
     * @param array $aParameters
     * @return int
     */
    public function updateContactNumber(int $userID, array $aParameters) : int
    {
        return $this->where('id', $userID)->update($aParameters);
    }
}
