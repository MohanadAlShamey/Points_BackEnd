<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUser(Request $request) {
        $users=User::where(function($query){
            $q=\request('search');
            $query->where('name','like','%'.$q.'%');
            $query->orWhere('email','like','%'.$q.'%');
            $query->orWhere('','like','%'.$q.'%');
        })->get();
        return response()->json(['users'=>UserResource::collection($users)],200);
    }
}
