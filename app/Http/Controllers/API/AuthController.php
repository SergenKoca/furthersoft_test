<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
  		if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user = Auth::user();
            $tokenResult =  $user->createToken('auth access token');
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

    public function register(Request $request){
    	$user = new User();
    	$user->name = $request->name;
    	$user->email = $request->email;
    	$user->password = bcrypt($request->password);

    	try {
    		$user->save();
    		return response()->json([
	            'message' => 'user created successfully',
	            'error' => false,
	            'data' => $user
        	], 200);
    	} catch (Exception $e) {
    		return response()->json([
	            'message' => $e->getMessage(),
	            'error' => true,
        	], 400);
    	}
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'successfully logged out'
        ]);
    }
}
