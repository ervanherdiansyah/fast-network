<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'referral',
        'first_buy_success',
        'first_order',
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
        'password' => 'hashed',
    ];

    use Notifiable;

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetails::class,  "user_id");
    }

    public function userWallet()
    {
        return $this->hasOne(UserWallet::class,  "user_id");
    }
    public function userBank()
    {
        return $this->hasMany(UserBank::class,  "user_id");
    }
    public function userAlamat()
    {
        return $this->hasMany(UserAlamat::class,  "user_id");
    }

    public function order()
    {
        return $this->hasMany(order::class,  "user_id");
    }

    // komisi history table!
    // affiliator_id -> user_id
    public function komisi_affiliator_id()
    {
        return $this->hasMany(UserKomisiHistory::class, "affiliator_id");
    }

    // affiliate_id -> user_id
    public function komisi_affiliate_id()
    {
        return $this->hasMany(UserKomisiHistory::class, "affiliate_id");
    }

    // affiliator komisi poin table! (Model AffiliatorPoinHistory, table AffiliatorPoinHistory)
    public function komisipoin_affiliate_id()
    {
        // affiliator_id -> user_id
        return $this->hasMany(AffiliatorPoinHistory::class,  "affiliate_id");
    }
    public function komisipoin_affiliator_id()
    {
        // affiliate_id -> user_id
        return $this->hasMany(AffiliatorPoinHistory::class, "affiliator_id");
    }

    public function totalHargaPerEnamBulan()
    {
        // affiliate_id -> user_id
        return $this->hasOne(OrderTotalHargaPerEnamBulan::class, "user_id");
    }
}
