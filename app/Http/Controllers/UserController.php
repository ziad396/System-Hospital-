<?php

namespace App\Http\Controllers;



use App\Http\Resources\UserResource;
use Carbon\Carbon;
use DB;
use Doctrine\Common\Lexer\Token;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Str;

class UserController extends Controller
{
    //
    public function register(Request $request){
          $validate=Validator::make($request->all(),[
        "name"=>"string|required|max:255",
        "email"=>"string|required|unique:users,email|max:255",
        "password"=>"string|required|min:6",
        "role"=>"string"
       ]);
       if ($validate->fails()) {

        # code...
        return response()->json([
            'errors'=>$validate->errors()
        ]);
       }
       $user=User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password),
        'role'=>$request->role
       ]);
       $token=$user->createToken('token')->plainTextToken;
       return response()->json([
        'user'=>new UserResource($user),
        'token'=>$token,
       ]);
    }
    public function login(Request $request){
        $validated=Validator::make($request->all(),[
            'email'=>'required|email|max:255',
            'password'=>'required|string|min:6',
        ]);
        if($validated->fails()){
            return response()->json([
                "errors"=>$validated->errors()
            ]);
        }
        $user=User::where('email',$request->email)->first();
        if ($user && Hash::check($request->password,$user->password)) {
            $token=$user->createToken('token')->plainTextToken;
            return response()->json([
                'message'=>'success',
                'token'=>$token,
            ]);
            # code...
        }
        return response()->json([
            'message'=>'Email or password not correct',
        ]);
    }
    public function forgetPassword(Request $request){
        
        $validated=Validator::make($request->all(),[
            'email'=>'required|string|max:255',
        ]);
       
        if ($validated->fails()) {
            # code...
            return response()->json([
              'errors'=>  $validated->errors(),
            ]);
        }
       
        $token=Str::random(64);
        $user=User::where('email',$request->email)->first();
        if (!$user) {
            # code...
            return response()->json([
                'message'=>'email not found',
            ]);
        }
       
        DB::table('password_reset_tokens')->where('email',$request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'=>$request->email,
            'token'=>Hash::make($token),
            'created_at'=>Carbon::now()
        ]);
        return response()->json([
            'message'=>'Done created token',
            'meassge'=>$token
        ]);
        
    }
      

    public function resetPassword(Request $request){
        $validated=Validator::make($request->all(),[
        'email'=>'required|email|exists:users,email',
        'token'=>'required',
        'new_password'=>'required|string|min:6',
        ]);
        if ($validated->fails()) {
            # code...
            return response()->json([
                'errors'=>$validated->errors(),
            ]);
        }
        $record=DB::table('password_reset_tokens')->where('email',$request->email)->first();
        if (!$record) {
            # code...
            return response()->json([
                'message'=>'Invalid token',
            ]);
        }
        if(Carbon::parse($record->created_at)->addMinutes(60)->isPast()){
            DB::table('password_reset_tokens')->where('email',$request->email)->delete();
            return response()->json([
                'message'=>'Token expired',
            ]);
        }
        if (Hash::check($request->token,$record->token)) {
            # code...
            $user=User::where('email',$request->email)->first();
            $user->password=Hash::make($request->new_password);
            $user->save();
            DB::table('password_reset_tokens')->where('email',$request->email)->delete();
            return response()->json([
                'message'=>'Password reset successfully',
            ]);

        }
    }
}

