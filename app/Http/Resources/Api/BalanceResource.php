<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
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
            'user'=>$this->user->name,
            'amount'=>$this->amount,
            'noID'=>$this->noID,
            'type'=>$this->type==1?'إضافة رصيد':'سحب رصيد',
            'qnt'=>$this->qnt,
            'note'=>$this->note,
            'status'=>$this->status==1?true:false,
            'userName'=>$this->balance->user->name
        ];
    }
}
