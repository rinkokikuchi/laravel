<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Auth; //18行目のowner_id取得のため
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;



class ImageController extends Controller
{
    public function __construct() //ログインしているかどうかの確認
    {
        $this->middleware('auth:owners');

        //ルートパラメータの注意…ログインしているownerしか見れない設定

        $this->middleware(function ($request, $next) {


            $id = $request->route()->parameter('image');
            if(!is_null($id)){
                $imagesOwnerId = Image::findOrFail($id)->owner->id;
                $imageId = (int)$imagesOwnerId;
                if($imageId !== Auth::id()){ //同じでなかったら
                    abort(404);              //不正なエラーを防ぐ関数
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        $images = Image::where('owner_id', Auth::id())
        ->orderBy('updated_at', 'desc') //orderBy desc=update_atを降順
        ->paginate(20);

        return view('owner.images.index',
        compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('owner.images.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UploadImageRequest $request)
    {
        $imageFiles = $request->file('files'); //('files) = name属性 複数の画像を配列として送るため
        if(!is_null($imageFiles)){
            foreach($imageFiles as $imageFile){
            $fileNameToStore = ImageService::upload($imageFile, 'products'); //第2引数はフォルダ名
            Image::create([  //保存処理
                'owner_id' => Auth::id(),
                'filename' => $fileNameToStore
            ]);
            }
        }

        return redirect()
        ->route('owner.images.index')
        ->with(['messege' => '画像登録を実施しました',
        'status' => 'info']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $image = Image::findOrFail($id);
        return view('owner.images.edit' , compact('image'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:50',
        ]);

        $image = Image::findOrFail($id);
        $image->title = $request->title;
        $image->save();

        return redirect()
        ->route('owner.images.index')
        ->with(['message'=> '画像情報を更新しました。',
        'status' => 'info']);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        $filePath = 'public/products/'.$image->filename; //ストレージのありか＋ファイルネームを取得

        $imageInProducts = Product::where('image1',$image->id)
        ->orwhere('image2',$image->id)
        ->orwhere('image3',$image->id)
        ->orwhere('image4',$image->id)
        ->get();

        if($imageInProducts){ //$imageInProducts = コレクション型
            $imageInProducts->each(function($product) use($image){
                if($product->image1 === $image->id){
                    $product->image1 = null;
                    $product->save();
                }
                if($product->image2 === $image->id){
                    $product->image2 = null;
                    $product->save();
                }
                if($product->image3 === $image->id){
                    $product->image3 = null;
                    $product->save();
                }
                if($product->image4 === $image->id){
                    $product->image4 = null;
                    $product->save();
                }
            });
        }

        if(Storage::exists($filePath)){ //ストレージにファイルパスがあれば
            Storage::delete($filePath); //ファイルパスを削除
        }

        Image::findOrFail($id)->delete();
        return redirect()
        ->route('owner.images.index')
        ->with(['message'=> '画像を削除しました。',
         'status' => 'alert']);
    }
}
