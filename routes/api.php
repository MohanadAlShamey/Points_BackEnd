<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login',[\App\Http\Controllers\Api\AuthController::class,'login']);
Route::post('register',[\App\Http\Controllers\Api\AuthController::class,'register']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('update',[\App\Http\Controllers\Api\AuthController::class,'update']);
    Route::post('changeAvatar',[\App\Http\Controllers\Api\AuthController::class,'changeAvatar']);
    Route::post('getUserByQr',[\App\Http\Controllers\Api\AuthController::class,'getUserByQr']);
    Route::post('getUserByEmail',[\App\Http\Controllers\Api\AuthController::class,'getUserByEmail']);


    Route::post('transfer',[\App\Http\Controllers\Api\PointController::class,'transfer']);
    Route::post('point/{point}/active',[\App\Http\Controllers\Api\PointController::class,'activePoint']);
    Route::post('point/{point}/cancel',[\App\Http\Controllers\Api\PointController::class,'cancelPoint']);
    Route::get('getMyPoints',[\App\Http\Controllers\Api\PointController::class,'getMyPoints']);

});

################################################################

######################## stuff Area ############################

############################################################3###

Route::middleware(['auth:sanctum','stuff'])->group(function(){
Route::post('generateQrCode',[\App\Http\Controllers\Api\AuthController::class,'generateQrCode']);
Route::post('newUser',[\App\Http\Controllers\Api\AuthController::class,'newUser']);

Route::post('addPoint',[\App\Http\Controllers\Api\PointController::class,'addPoint']);
Route::post('pullPoint',[\App\Http\Controllers\Api\PointController::class,'pullPoint']);
    Route::get('getAllPoints',[\App\Http\Controllers\Api\PointController::class,'getAllPoints']);
});
