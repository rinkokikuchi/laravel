<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use App\Models\Stock;
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


    public function checkout()
    {
        $user = User::findOrFail(Auth::id()); //ログインしているユーザー情報を取得
        $products = $user->products;

        $lineItems=[];
        foreach($products as $product){
            $quantity = '';
            $quantity = Stock::where('product_id', $product->id)->sum('quantity');


            if($product->pivot->quantity > $quantity)
            //カート内の数量 > stockテーブルの数量
            {
              //  return redirect()->route('user.cart.index');
              //return redirect()->route('user.cart.index');
              return view('user.cart.index',compact('product'));
            }else{
                $lineItem=[
                    'name' => $product->name,
                    'description' => $product->information,
                    'amount' => $product->price,
                    'currency' => 'jpy',
                    'quantity' => $product->pivot->quantity,
                ];
                array_push($lineItems,$lineItem);
            }
        }
        foreach($products as $product)
        {
            Stock::create([
                //カートに入れた分商品を減らしておく
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1
            ]);
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.items.index'),
            'cancel_url' => route('user.cart.index')
        ]);
        $publicKey = env('STRIPE_PUBLIC_KEY');



        return view('user.checkout' , compact('session', 'publicKey'));

    }
}
