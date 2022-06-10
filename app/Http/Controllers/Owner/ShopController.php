<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth; //18行目のowner_id取得のため

class ShopController extends Controller
{
    public function __construct() //ログインしているかどうかの確認
    {
        $this->middleware('auth:owners');

        //ルートパラメータの注意…ログインしているownerしか見れない設定

        $this->middleware(function ($request, $next) {
            // dd($request->route()->parameter('shop')); //string
            // dd( Auth::id()); //int

            $id = $request->route()->parameter('shop');
            if(!is_null($id)){
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId;
                $ownerId = Auth::id();
                if($shopId !== $ownerId){
                    abort(404); //不正なエラーを防ぐ関数
                }
            }
            return $next($request);
        });
    }

    public function index() //表示
    {
        $ownerId = Auth::id();
                //ログインしているownerのid
        $shops = Shop::where('owner_id', $ownerId)->get();
                //Shopモデルでowner_idで検索(where)し、一致したものをget

        return view('owner.shops.index',
        compact('shops'));
    }

    public function edit($id) //編集
    {
        dd(Shop::findOrFail($id));
    }

    public function update(Request $request, $id)
    {

    }
}
