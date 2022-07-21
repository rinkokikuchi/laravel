<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function index()
    {
        $user = User::findOrFail(Auth::id()); //ログインしているユーザー情報を取得
        $products = $user->products;
        $totalPrice = 0;

        foreach($products as $product){
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        //dd($products,$totalPrice);

        return view('user.cart',
        compact('products', 'totalPrice'));
    }
    public function add(Request $request)
    {
        $itemInCart = Cart::where('product_id',$request->product_id) //cartクラスの中で探す($request->product_idを'product_id'と認識する)
        ->where('user_id',Auth::id())->first();

        if($itemInCart)
        {
            $itemInCart->quantity += $request->quantity;
            $itemInCart->save();
        }else{
            Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity
            ]);
        }
        return redirect()->route('user.cart');
    }

    public function delete($id)
    {
        Cart::where('product_id' , $id)
        ->where('user_id' , Auth::id())->delete();

        return redirect()->route('user.cart.index');
    }
}
