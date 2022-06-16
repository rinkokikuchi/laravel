<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth; //18行目のowner_id取得のため
use Illuminate\Support\Facades\Storage;
use InterventionImage;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;

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
        $shop = Shop::findOrFail($id);
        return view('owner.shops.edit' , compact('shop'));
    }

    public function update(UploadImageRequest $request, $id)
    {

        $request->validate([
            'name' => 'required', 'string', 'max:50',
            'information' => 'required', 'string', 'email', 'max:1000',
            'is_selling' => 'required',
            //required = 必須項目　confirme = 確認する
        ]);

        $imageFile = $request->image;

        if(!is_null($imageFile) && $imageFile->isValid())
        {
            $fileNameToStore = ImageService::upload($imageFile, 'shops');
        }

        $shop = Shop::findOrFail($id);
        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;

        if(!is_null($imageFile) && $imageFile->isValid()){
            $shop->filename = $fileNameToStore;
        }

        $shop->save();


        return redirect()
        ->route('owner.shops.index')
        ->with(['message', '店舗情報を更新しました。',
        'status' => 'info']);
    }
}
