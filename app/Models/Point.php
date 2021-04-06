<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $guarded=[];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function point(){
        return $this->hasOne(Point::class);
    }

    public function getCreatedAtAttribute($valus){
        return Carbon::parse($valus)->format('Y-m-d');
    }
}
