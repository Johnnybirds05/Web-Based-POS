<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuantityTransaction;
use App\Models\ChangeQuantity;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function store(Request $request)
    {

        $quantity_transactions = QuantityTransaction::create([
            'user_id' => Auth::user()->user_id
        ]);

        foreach($request->all() as $key => $item){
           Product::where('product_id', $item['product_id'])
            ->update([
                'total_quantity' => DB::raw('total_quantity + ' .$item['quantity']),
                'current_quantity' => DB::raw('current_quantity + ' . $item['quantity']),
                'error' => DB::raw('error + ' . $item['error'])
            ]);
            ChangeQuantity::insert([
                'change_quantity_id' => $quantity_transactions->change_quantity_id,
                'product_id' => $item['product_id'],
                'error' => $item['error'],
                'quantity' => $item['quantity']
            ]);
        }

        return response()->json([
            'status' => 'Stock save successfully!'
        ]);
    }

    public function edit(string $id)
    {
        return ChangeQuantity::where('change_quantity_id',$id)->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id,Request $request)
    {

        $products = ChangeQuantity::where('change_quantity_id',$id)->get();
        foreach($products as $product){
            Product::where('product_id', $product->product_id)
             ->update([
                 'total_quantity' => DB::raw('total_quantity - ' .$product->quantity),
                 'current_quantity' => DB::raw('current_quantity - ' .$product->quantity),
                 'error' => DB::raw('error - ' . $product->quantity)
             ]);
        }
        ChangeQuantity::destroy('change_quantity_id',$id);

        foreach($request->all() as $key => $item){
            Product::where('product_id', $item['product_id'])
             ->update([
                 'total_quantity' => DB::raw('total_quantity + ' .$item['quantity']),
                 'current_quantity' => DB::raw('current_quantity + ' . $item['quantity']),
                 'error' => DB::raw('error + ' . $item['error'])
             ]);

             ChangeQuantity::insert([
                 'change_quantity_id' => $id,
                 'product_id' => $item['product_id'],
                 'error' => $item['error'],
                 'quantity' => $item['quantity']
             ]);
         }
 
         return response()->json([
             'status' => 'Stock successfully updated!'
         ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $products = ChangeQuantity::where('change_quantity_id',$id)->get();
            foreach($products as $product){
                Product::where('change_quantity_id',$id)->update([
                'total_quantity' => DB::raw('total_quantity - ' .$product->quantity),
                'current_quantity' => DB::raw('current_quantity - ' .$product->quantity),
                'error' => DB::raw('error - ' . $product->quantity)
            ]);      ChangeQuantity::destroy('change_quantity_id',$id);
            QuantityTransaction::destroy($id);
            return response()->json(['status'=>'Transaction Successfully Deleted!']);
        }
    }
    public function fetchAllProducts()
    {
        return Product::select('product_id','product_name')->get();
        return response()->json();
    }
}
