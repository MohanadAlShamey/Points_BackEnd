<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $appends=['balance','totalBalance'];
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'img',
        'qr',
        'code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function roles(){
        return $this->belongsToMany(Role::class);
    }


    public function hasRole($role){
        $r= $this->roles()->where('name',$role)->first();
        if(!is_null($r)){
            return true;
        }
        return false;
    }

    public function points(){
        return $this->hasMany(Point::class);
    }

    public function getBalanceAttribute(){
        $add=$this->points()->where(['type'=>1,'status'=>1])->sum('qnt');
        $min=$this->points()->where(['type'=>-1])->sum('qnt');
        return $add-$min;
    }

    public function getTotalBalanceAttribute(){
        $add=$this->points()->where(['type'=>1])->sum('qnt');
        $min=$this->points()->where(['type'=>-1])->sum('qnt');
        return $add-$min;
    }


}
