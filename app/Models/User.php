<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const SELLER_ROLE = 'seller';
    const BYUER_ROLE = 'buyer';

    public function getRoles(){
        return [
            self::SELLER_ROLE,
            self::BYUER_ROLE,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'deposit',
        'role',
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

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function AauthAcessToken(){
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function calculateChange($value){
        $value_array = [];

        while(true){
            if($value >= 100){
                $value_array[] = 100;
                $value = $value - 100;
            }elseif($value < 100 && $value >= 50){
                $value_array[] = 50;
                $value = $value - 50;
            }elseif($value < 50 && $value >= 20){
                $value_array[] = 20;
                $value = $value - 20;
            }elseif($value < 20 && $value >= 10){
                $value_array[] = 10;
                $value = $value - 10;
            }elseif($value < 10 && $value >= 5){
                $value_array[] = 5;
                $value = $value - 5;
            }elseif($value < 5){
                break;
            }
        }

        return $value_array;
    }

    public function products(){
        return $this->hasMany(Product::class);
    }
}
