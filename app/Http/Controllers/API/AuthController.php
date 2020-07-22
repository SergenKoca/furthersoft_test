<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use http\Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // sisteme giriş yapma metodu
    public function login(Request $request){
  		if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user = Auth::user();
            $tokenResult =  $user->createToken('auth access token'); // kullanıcıya benzersiz bir token verilme işlemi
            return response()->json([
            	'message' => 'login successful',
            	'tokenResult' => $tokenResult,
            	'data' => $user
            ], 200);
        }
        else{
            return response()->json([
            	'message'=>'login failed'
            ], 401);
        }
    }

    // sisteme kayıt olma metodu
    public function register(Request $request){
        if(!isset($request->name) or !isset($request->email) or !isset($request->password)){
            return response()->json([
               'error' => true,
               'message' => 'bilgilerinizi kontrol edin.',
            ],400);
        }

    	$user = new User();
    	$user->name = $request->name;
    	$user->email = $request->email;
    	$user->password = bcrypt($request->password);

        $user->save();
        return response()->json([
            'message' => 'user created successfully',
            'error' => false,
            'data' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke(); // aktif olan kulanıcının token'inı silerek oturumdan düştüğü belirtilir.
        return response()->json([
            'message' => 'successfully logged out'
        ],200);
    }
}
