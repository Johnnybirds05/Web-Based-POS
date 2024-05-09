<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'first_name',
        'middle_initial',
        'last_name',
        'role',
        'contact',
        'username',
        'password',
        'img'
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
        'password' => 'hashed',
    ];

    public function transaction(){
        return $this->hasMany(Transaction::class,'user_id','user_id');
    }
    public function change_cost(){
        return $this->hasMany(ChangeCost::class,'user_id','user_id');
    }
    public function change_quantity(){
        return $this->hasMany(ChangeQuantity::class,'user_id','user_id');
    }
}
