<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    public function addPoint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'noId' => 'required|unique:points,noId',
            'amount' => 'required',
            'qnt' => 'required',
        ]);

        if ($validator->fails()) {
            if (array_key_exists('noId', $validator->errors()->toArray())) {
                return response()->json([], 401);
            }
            return response()->json([], 422);
        }

        $user = User::where('code', $request->code)->first();
        try {
            Point::create([
                'type' => 1,
                'status' => 1,
                'amount' => $request->amount,
                'user_id' => $user->id,
                'noId' => $request->noId,
                'qnt' => $request->qnt,
                'note' => $request->note,
            ]);
            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json([], 404);
        }


    }

    public function transfer(Request $request)
    {
        $validat = Validator::make($request->all(), [
            'email' => 'required',
            'qnt' => 'required',
        ]);

        if (auth()->user()->balance < $request->qnt) {
            return response()->json([], 402);
        }
        $user = User::where('email', $request->email)->first();

        DB::beginTransaction();
        try {
            $from = Point::create([
                'qnt' => $request->qnt,
                'user_id' => auth()->id(),
                'status' => 1,
                'type' => -1
            ]);

            $to = Point::create([
                'qnt' => $request->qnt,
                'user_id' => $user->id,

            ]);

            $from->update([
                'point_id' => $to->id,
            ]);
            $to->update([
                'point_id' => $from->id,
            ]);

            DB::commit();

            return response()->json([], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([$e->getMessage()], 401);
        }


    }


    public function activePoint(Point $point)
    {
        if (auth()->id() == $point->user_id) {
            $point->update([
                'status' => 1
            ]);
            return response()->json(['user' => $point->user], 200);
        }
        return response()->json([], 422);
    }


    public function cancelPoint(Point $point){
        if (auth()->id() != $point->user_id) {
            return response()->json([],422);

        }
      DB::beginTransaction();
      try{
          $point->point()->delete();
          $point->delete();
          DB::commit();
          return response()->json(['user' => auth()->user()], 200);
      }catch(\Exception $e){
          DB::rollBack();
          return response()->json([],401);
      }
    }

    public function pullPoint(Request $request)
    {

        $user = User::where('code', $request->code)->first();
        if ($request->qnt > $user->balance) {
            return response()->json([], 401);
        }


        if (is_null($user)) {
            return response()->json([], 404);
        }

        Point::create([
            'user_id' => $user->id,
            'qnt' => $request->qnt,
            'type' => -1,
            'note' => $request->note,
            'status'=>1,
        ]);
        return response()->json([], 200);
    }


    public function getMyPoints(Request $request){

        $points=Point::where('user_id',auth()->id())->when(!is_null($request->date),function ($query){
            $query->where('created_at','like','%'.\request()->get('date').'%');
        })->latest()->get();

        return response()->json(['points'=>$points],200);

    }

    public function getAllPoints(Request $request){

        $points=Point::where('user_id',auth()->id())->where(function ($query){
            $query->where('created_at','like','%'.\request()->get('search').'%');
            $query->orWhere('noID','like','%'.\request()->get('search').'%');
            $query->orWhereHas('user',function($query){
                $query->where('name','like','%'.\request()->get('search').'%');
                $query->orWhere('email','like','%'.\request()->get('search').'%');
            });
        })->latest()->get();

        return response()->json(['points'=>$points],200);

    }
}
