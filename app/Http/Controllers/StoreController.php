<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    //
    public function show(){
        return view('store',[
            'product_categories' => ProductCategory::all(),
        ]);
    }
}
