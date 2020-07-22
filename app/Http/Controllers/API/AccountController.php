<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function createAccount(){
        $user_id = Auth::id();
        $account = new Account();
        $account->user_id = $user_id;
        $account->balance = 0;
        $account->active = true;
        $account->save();

        return response()->json([
            'error' => false,
            'message' => 'hesap başarıyla oluşturuldu',
            'data' => $account
        ],200);
    }

    public function showAccount(){
        $user_id = Auth::id();
        $accounts = Account::where('user_id',$user_id)->get();
        return response()->json([
            'data' => $accounts,
            'message' => 'hesaplar başarıyla getirildi.'
        ]);
    }

    public function getAccount($id){
        $account = Account::find($id);
        return response()->json([
            'error' => false,
            'data' => $account,
            'message' => 'hesap başarıyla getirildi'
        ]);
    }

    public function deleteAccount($id){
        $account = Account::find($id);
        $account->delete();
        return response()->json([
            'error' => false,
            'message' => 'hesap başarıyla silindi'
        ]);
    }

    public function activeAccount($id){
        $account = Account::find($id);
        $account->active = true;
        $account->save();
        return response()->json([
            'error' => false,
            'data' => $account,
            'message' => 'hesap başarıyla aktif edildi'
        ]);
    }

    public function passiveAccount($id){
        $account = Account::find($id);
        $account->active = false;
        $account->save();
        return response()->json([
            'error' => false,
            'data' => $account,
            'message' => 'hesap başarıyla pasif edildi'
        ]);
    }

    public function depositAccount($id,Request $request){
        if($request->quantity <=0 ){
            return response()->json([
               'error' => true,
               'message' => 'sıfırdan büyük bir miktar girin'
            ]);
        }

        $account = Account::find($id);
        if($account->balance < 0){
            $total= $request->quantity-(($request->quantity*2)/100);
            $account->balance += $total;
            $account->save();

            return response()->json([
                'data' => $account,
                'message' => 'Para yatırıldı, %2 tutarında bir kesinti yapıldı.'
            ],200);
        }
        else{
            $account->balance += $request->quantity;
            $account->save();

            return response()->json([
                'data' => $account,
                'message' => 'Para yatırıldı'
            ],200);
        }
    }

    public function withdrawAccount($id,Request $request){
        if($request->quantity <=0 ){
            return response()->json([
                'error' => true,
                'message' => 'sıfırdan büyük bir miktar girin'
            ]);
        }

        $account = Account::find($id);
        if($account->balance <= -500){
            return response()->json([
                'message' => 'hesabınızda 500 liradan az para olduğu için para çekemezsiniz.'
            ],200);
        }
        else{
            if($account->balance - $request->quantity < -500){
                return response()->json([
                    'data' => $account,
                    'message' => '-500 limiti aşıldı. Para çekme başarısız.'
                ],200);
            }
            else{
                $account->balance -= $request->quantity;
                $account->save();

                return response()->json([
                    'data' => $account,
                    'message' => 'para başarıyla çekildi'
                ],200);
            }

        }
    }
}
