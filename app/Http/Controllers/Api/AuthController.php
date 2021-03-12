<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Tools\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Generator;

class AuthController extends Controller
{
    use ImageHelper;

    public function login(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([], 401);
        }
        if (!auth()->attempt(['email' => $request->username, 'password' => $request->password])
            && !auth()->attempt(['username' => $request->username, 'password' => $request->password])) {
            return response([], 422);
        }
        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->username);
            $query->orWhere('username', $request->username);
        })->first();
        return response()->json(['user' => $user, 'balance' => $user->balance,'access_token'=>$user->createToken('user_token')->plainTextToken], 200);
    }

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8'
        ]);

        if ($validate->fails()) {
            if (array_key_exists('username', $validate->errors()->toArray())) {
                return response()->json([], 402);
            }
            if (array_key_exists('email', $validate->errors()->toArray())) {
                return response()->json([], 403);
            }

            return response()->json([$validate->getMessageBag()], 422);
        }
        $qrcode=new Generator();
        $name='images/'.time().Str::random(3).'.svg';
        $code=md5(time().Str::random(4));
        $qrcode->generate($code,storage_path('app/public/'.$name));
        //return $data;
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'qr'=>$name,
            'code'=>$code
        ]);

        return response()->json(['user' => $user, 'balance' => $user->balance], 200);
    }


    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . auth()->id(),
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'password' => 'nullable|min:8'
        ]);


        if ($validate->fails()) {
            if (array_key_exists('username', $validate->errors()->toArray())) {
                return response()->json([], 402);
            }
            if (array_key_exists('email', $validate->errors()->toArray())) {
                return response()->json([], 403);
            }

            return response()->json([$validate->getMessageBag()], 422);
        }

        $user = auth()->user();

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        $user->save();
        return response()->json(['user' => $user, 'balance' => $user->balance], 200);

    }

    public function changeAvatar(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'img' => $this->uploadImg($request->avatar, 64, 64, false, auth()->user()->img),
        ]);
        return response()->json(['user' => $user, 'balance' => $user->balance], 200);
    }


    public function getUserByQr(Request $request){
        $user=User::where('code',$request->code)->first();
        if(is_null($user)){
         return response()->json([],404);
        }
        return response()->json(['user' => $user], 200);
    }


    public function generateQrCode(){
        $count=time();
        $users=User::all();

        foreach ($users as $user) {
            $qrcode=new Generator();
            $old=storage_path('app/public/'.$user->qr);
            $name='images/'.$count.Str::random(3).'.svg';
            $code=md5($count.Str::random(4));
            if(is_file($old)&& file_exists($old)){
                unlink($old);
            }
            $qrcode->generate($code,storage_path('app/public/'.$name));

            $user->update([
                'qr'=>$name,
                'code'=>$code,
            ]);

        }
        return response()->json([],200);
    }


    public function newUser(Request $request){
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8'
        ]);

        if ($validate->fails()) {
            if (array_key_exists('username', $validate->errors()->toArray())) {
                return response()->json([], 402);
            }
            if (array_key_exists('email', $validate->errors()->toArray())) {
                return response()->json([], 403);
            }

            return response()->json([$validate->getMessageBag()], 422);
        }
        $qrcode=new Generator();
        $name='images/'.time().Str::random(3).'.svg';
        $code=md5(time().Str::random(4));
        $qrcode->generate($code,storage_path('app/public/'.$name));
        //return $data;
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'qr'=>$name,
            'code'=>$code
        ]);

        return response()->json([], 200);
    }

    public function getUserByEmail(Request $request){
        $user=User::where('email',$request->email)->first();

        if(is_null($user)){
          return response()->json([],404);
        }
         return response()->json(['user' => $user], 200);
    }
}
