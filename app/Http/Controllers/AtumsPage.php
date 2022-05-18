<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AtumsPage extends Controller
{
   public function showAtumsPage(){
        $message = "Hello Atums";
        return view('tests.atumspage-test');
   }
    //
}
