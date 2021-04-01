<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PointResource;
use App\Http\Resources\Api\UserResource;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    # @Stuff
    public function addPoint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'noId' => 'required|unique:points,noId',// orderId
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

    # @Stuff
    public function pullPoint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'qnt' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json([], 422);
        }
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
            'status' => 1,
        ]);
        return response()->json([], 200);
    }

    # @Stuff
    public function getAllPoints(Request $request, User $user)
    {

        $points = Point::where('user_id', $user->id)->where(function ($query) {
            if (!empty(\request()->get('search'))) {
                $query->orWhere('noID', 'like', '%' . \request()->get('search') . '%');
            }


        })->latest()->get();

        return response()->json(['points' => PointResource::collection($points)], 200);

    }

    ###################################################
    ###                                             ###
    ###                    User                     ###
    ###                                             ###
    ###################################################

    # @User
    public function transfer(Request $request)
    {
        /* $validat = Validator::make($request->all(), [
             'email' => 'required',
             'qnt' => 'required',
         ]);*/
        $user = User::where('idNo', $request->idNo)->first();
        //return $user;
        if ($user->id == auth()->id()) {
            return response()->json([], 403);
        }

        if (auth()->user()->balance < $request->qnt) {
            return response()->json([], 402);
        }

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

            return response()->json(['user' => new UserResource(auth()->user())], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([$e->getMessage()], 401);
        }


    }

    # @USer
    public function activePoint(Point $point)
    {
        if (auth()->id() == $point->user_id) {
            $point->update([
                'status' => 1
            ]);
            return response()->json(['user' => new UserResource(auth()->user())], 200);
        }
        return response()->json([], 422);
    }

    # @User
    public function cancelPoint(Point $point)
    {
        if (auth()->id() != $point->user_id) {
            return response()->json([], 422);

        }
        DB::beginTransaction();
        try {
            $point->point()->delete();
            $point->delete();
            DB::commit();
            return response()->json(['user' => new UserResource(auth()->user())], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([], 401);
        }
    }

    # @User
    public function getMyPoints(Request $request)
    {

        $points = Point::where('user_id', auth()->id())->when(!is_null($request->date), function ($query) {
            $query->where('created_at', 'like', '%' . \request()->get('date') . '%');
        })->latest()->get();

        return response()->json(['points' => PointResource::collection($points)], 200);

    }

}
