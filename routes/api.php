<?php

use App\Http\Controllers\BookConroller;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Models\Appiontment;
use Illuminate\Support\Facades\Route;


Route::get('/test',function(){
    return response()->json([
        'message'=>'Api file working fine,I am ziad'
    ]);
    });
  
Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::post('forget-password',[UserController::class,'forgetPassword']);
Route::post('reset-password',[UserController::class,'resetPassword']);
//doctor
Route::fallback(function(){
return response()->json([
'message'=>'page not found'
]);
}); 

Route::resource('doctor',DoctorController::class)->middleware(['auth:sanctum','doctor']);
Route::resource('schedule',ScheduleController::class)->middleware(['auth:sanctum','doctor']);
Route::get('schedule/validate/{id}',[ScheduleController::class,'scheduleValidate'])->middleware(['auth:sanctum','doctor']);
// Route::get('book',BookConroller::class)->middleware(['auth:sanctum','doctor']);
Route::get('book',[BookConroller::class,'index'])->middleware(['auth:sanctum','doctor']);
Route::post('book/{id}',[BookConroller::class,'store'])->middleware(['auth:sanctum','doctor']);