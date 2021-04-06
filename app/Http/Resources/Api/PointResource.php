<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
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
            'amount'=>$this->amount??0,
            'noID'=>$this->noID??'',
            'type'=>$this->type==1?'إضافة رصيد':'سحب رصيد',
            'qnt'=>$this->qnt,
            'note'=>$this->note??'',
            'status'=>$this->status==1?true:false,
            'userName'=>$this->getUserNameByType(),
            'createdAt'=>$this->created_at,
        ];
    }

   public function getUserNameByType(){
        if($this->point!=null){
            return $this->point->user->name;
        }else{
            return '';
        }
    }
}
