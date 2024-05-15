<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuantityTransaction;
use App\Models\ChangeQuantity;
use App\Models\Product;

class QuantityChangeController extends Controller
{
    public function index()
    {
        return QuantityTransaction::all();
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

    }

    public function edit(string $id)
    {
        return QuantityTransaction::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id,Request $req)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

    }
    public function fetchAllProducts()
    {
        return Product::select('product_id','product_name')->get(); 
    }
}
