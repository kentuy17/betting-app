<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Traits\HasRoles;
use App\Models\ShareHolder;
use App\Models\ModelHasRoles;
use App\Models\Roles;
use Shetabit\Visitor\Traits\Visitor;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Visitor;

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
        'points',
        'active',
        'defaultpassword'
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
        'created_at' => 'datetime:m/d/y h:s',
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

    public function share_holder()
    {
        return $this->hasOne(ShareHolder::class, 'user_id');
    }

    public function active_commission()
    {
        return $this->hasMany(Commission::class, 'user_id')->where('active',true);
    }

    public function model_has_roles()
    {
        return $this->hasMany(ModelHasRoles::class, 'model_id', 'id')->with('roles');
    }

    public function _user_has()
    {
        return $this->model_has_roles()->get()->pluck('roles');
    }

    public function _user_permissions()
    {
        return $this->_user_has()->pluck('name');
    }

    public function user_role()
    {
        return $this->hasOne(Roles::class, 'id', 'role_id');
    }
}
