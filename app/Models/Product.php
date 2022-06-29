<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\Image;
use App\Models\Stock;
use App\Models\SecondaryCategory;


class Product extends Model
{
    use HasFactory;

    public function Shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(SecondaryCategory::class, 'secondary_category_id');
    }

    public function imageFirst()  //tableと一緒はNG 'image1'
    {
        return $this->belongsTo(Image::class,'image1','id');
    }

    public function stock(){
        return $this->hasMany(Stock::class);
    }
}