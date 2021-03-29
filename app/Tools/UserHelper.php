<?php


namespace App\Tools;




use App\Models\User;

trait UserHelper
{

    public function generateCode()
    {
        $arr=[0,1,2,3,4,5,6,7,8,9];
        test:
        $rand='';
     for($i=0;$i<9;$i++){
       $shuffle=rand(0,8);
       $rand.=$arr[$shuffle];
     }
     $user=User::where('idNo',$rand)->first();
     if($user!=null){
         goto test;
     }
      return $rand;
    }

}
