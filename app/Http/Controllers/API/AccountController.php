<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // sisteme giriş yapan kullanıcının hesap oluşturabileceği metot
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

    // sisteme giriş yapan kullanıcının tüm hesaplarını görüntülendiği metot
    public function showAccount(){
        $user_id = Auth::id();
        $accounts = Account::where('user_id',$user_id)->get();
        return response()->json([
            'data' => $accounts,
            'message' => 'hesaplar başarıyla getirildi.'
        ]);
    }

    // sisteme giriş yapan kullanıcının belirli bir hesabını görüntülendiği metot
    public function getAccount($id){
        $account = Account::find($id);
        return response()->json([
            'error' => false,
            'data' => $account,
            'message' => 'hesap başarıyla getirildi'
        ]);
    }

    // sisteme giriş yapan kullanıcının belirli bir hesabını sildiği metot
    public function deleteAccount($id){
        $account = Account::find($id);
        $account->delete();
        return response()->json([
            'error' => false,
            'message' => 'hesap başarıyla silindi'
        ]);
    }

    // sisteme giriş yapan kullanıcının belirli bir hesabını aktif ettiği metot
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

    // sisteme giriş yapan kullanıcının belirli bir hesabını pasif ettiği metot
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

    // sisteme giriş yapan kullanıcının belirli bir hesabına para yatırdığı metot
    public function depositAccount($id,Request $request){
        if($request->quantity <=0 ){ // sıfır veya daha küçük para yatırılamaz. Bu durumun kontrolü
            return response()->json([
               'error' => true,
               'message' => 'sıfırdan büyük bir miktar girin'
            ]);
        }

        $account = Account::find($id);
        if($account->balance < 0){ // hesapda sıfırın altına para var ise banka yatırılan paranın %2'sini alır.
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

    // sisteme giriş yapan kullanıcının belirli bir hesabından para çektiği metot
    public function withdrawAccount($id,Request $request){
        if($request->quantity <=0 ){  // sıfır veya daha küçük para çekilemez. Bu durumun kontrolü
            return response()->json([
                'error' => true,
                'message' => 'sıfırdan büyük bir miktar girin'
            ]);
        }
        if($request->quantity > 500){ // 500'den fazla para çekilemez. Bu durumun kontrolü
            return response()->json([
                'error' => true,
                'message' => 'tek seferde en fazla 500 çekilebilir'
            ]);
        }

        $account = Account::find($id);
        if($account->balance <= -500){ // hesapta -500 lira veya daha az para olduğu durumlarda para çekilemez. Bu durumun kontrolü
            return response()->json([
                'message' => 'hesabınızda -500 liradan az para olduğu için para çekemezsiniz.'
            ],200);
        }
        else{
            if($account->balance - $request->quantity < -500){ // para çekildiğinde -500'ü geçerse para çekilemez. Bu durumun kontrolü
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

    // sisteme giriş yapan kullanıcının belirli bir hesabının bakiyesini görüntülediği metot
    public function balanceAccount($id){
        $account = Account::find($id);
        return response()->json([
            'error' => false,
            'data' => 'hesap bakiyesi '.$account->balance,
            'message' => 'hesap bakiyesi başarıyla getirildi'
        ],200);
    }
}
