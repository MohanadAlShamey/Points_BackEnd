<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPUnit\Exception;
use SimpleSoftwareIO\QrCode\Generator;

class LockController extends Controller
{
    public function generateCode()
    {
        $qrcode = new Generator();
        $name = 'images/' . time() . Str::random(3) . '.svg';
        $code = md5(time() . Str::random(4));
        $qrcode->generate($code, storage_path('app/public/' . $name));
        $lock=Lock::where('id',1)->first();
        if($lock!=null){
            try{
                $lock->update([
                    'code'=>$code,
                    'img'=>$name,
                ]);
            }catch (Exception $e){
                return response()->json([],401);
            }

        }else{
            try{
                Lock::query()->delete();
                Lock::create([
                    'id'=>1,
                    'code'=>$code,
                    'img'=>$name
                ]);
            }catch (Exception $e){
                return response()->json([],401);
            }

        }
       return response()->json([],200);
    }

    public function getQrLock()
    {
        $lock=Lock::first();
        if($lock!=null){
            return response()->json(['img'=>asset('storage/'.$lock->img)],200);
        }
        return response()->json([],401);

    }

    ############ stuff

    public function unlock(Request  $request){
        $lok=Lock::where('code',$request->code)->first();
        if($lok==null){
            return response()->json([],401);
        }
        return response()->json(['code'=>$lok->code],200);
    }
}
