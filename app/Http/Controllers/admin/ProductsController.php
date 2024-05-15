<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $req)
    {
        $req->validate([
            'product_name' => ['required','string'],
            'category'=>['required','string'],
            'description'=>['nullable','string'],
            'original_price'=>['required','numeric'],
            'retail_price'=>['required','numeric'],
            'quantity_value'=>['required','string'],
        ]);

        Product::create(
            [
                'product_name'=> $req->product_name,
                'category'=> $req->category,
                'description'=> $req->description?$req->description:'',
                'original_price'=> $req->original_price,
                'retail_price'=> $req->retail_price,
                'quantity_value'=> $req->quantity_value,
            ]
            );
            return response()->json([
                'status' => 'Product successfully saved!'
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id,Request $req)
    {
        $req->validate([
            'product_name' => ['required','string'],
            'category'=>['required','string'],
            'description'=>['nullable','string'],
            'original_price'=>['required','numeric'],
            'retail_price'=>['required','numeric'],
            'quantity_value'=>['required','string'],
        ]);
        Product::where('product_id',$req->product_id)
        ->update(
            [
                'product_name'=> $req->product_name,
                'category'=> $req->category,
                'description'=> $req->description?$req->description:'',
                'original_price'=> $req->original_price,
                'retail_price'=> $req->retail_price,
                'quantity_value'=> $req->quantity_value,
            ]
            );
            return response()->json([
                'status' => 'Product successfully saved!'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json([
            'status' => 'Products Successfully Deleted!'
        ]);
    }

    public function destroyMultiple(Request $request)
    {
        foreach ($request->all() as $key => $id) {
            Product::destroy($id);
        }
        return response()->json([
            'status' => 'Multiple Products Successfully Deleted!'
        ]);
    }
}
