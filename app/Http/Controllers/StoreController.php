<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StoreController extends Controller
{
    //
    public function show(){
        return view('store',[
            // 'products' => Product::with(['product_category'])->get(),
            'products' => Product::where('stock', '>', 0)->with(['product_category'])->get(),
        ]);
    }

    public function product_insert_form(){
        return view('product.insert-form',[
            'product_categories' => ProductCategory::get(),
        ]);
    }

    public function insert_product(Request $request){
        if(!Gate::allows('insert-product')){
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'product_category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ],[
            'name.required' => 'Product name is required.',
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'details.string' => 'Product details must be a string.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 1.',
            'stock.required' => 'Stock is required.',
            'stock.integer' => 'Stock must be an integer.',
            'stock.min' => 'Stock cannot be negative.',
            'product_category.required' => 'Product category is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);
        
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time(). '-' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('product_image'), $imageName);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->details = $request->details;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->category_id = $request->product_category;
        $product->image_path = $imageName;

        $product->save();

        return redirect()->route('store')->with('success', 'Product inserted successfully!');
    }

    public function product_edit_form($product_id){
        return view('product.edit-form',[
            'product' => Product::findOrFail($product_id),
            'product_categories' => ProductCategory::get(),
        ]);
    }

    public function update_product(Request $request, $product_id){
        if(!Gate::allows('update-product')){
            abort(403, 'Unauthorized');
        }
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'product_category' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Max 2MB
        ], [
            'name.required' => 'Product name is required.',
            'price.required' => 'Product price is required.',
            'price.min' => 'Product price must be at least 1.',
            'stock.required' => 'Product stock is required.',
            'stock.min' => 'Product stock must be at least 0.',
            'product_category.required' => 'Product category is required.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
            'image.max' => 'The image may not be greater than 2MB.',
         ]
        );

        $product = Product::findOrFail($product_id);

        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                unlink(public_path('product_image/' . $product->image_path));
            }
            $imageName = time() . '-' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('product_image'), $imageName);
            $product->image_path = $imageName;
        }

        $product->name = $request->name;
        $product->details = $request->details;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->category_id = $request->product_category;

        $product->save();

        return redirect()->route('store')->with('success', 'Product updated successfully!');
    }

    public function delete_product($product_id){
        if(!Gate::allows('delete-product')){
            abort(403, 'Unauthorized');
        }
        $product = Product::findOrFail($product_id);
        $product->delete();
        return redirect()->route('store')->with('success', 'Product deleted successfully!');
    }
}
