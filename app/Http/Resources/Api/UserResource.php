<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'username'=>$this->username,
            'email'=>$this->email,
            'avatar'=>!empty($this->img)?asset('storage/'.$this->img):asset('images/avatar.png'),
            'qr'=>asset('storage/'.$this->qr),
            'balance'=>$this->balance,
            'total'=>$this->totalBalance,
            'phone'=>$this->phone??'',
            'idNo'=>$this->idNo,
            'mony'=>$this->mony,
            'totalMony'=>$this->totalMony,
            'role'=>$this->role,
            'deviceToken'=>$this->device_token
        ];
    }
}
