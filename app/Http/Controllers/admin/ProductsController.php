<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductsController extends Controller
{
    public function index()
    {
        return Product::with(['transactions' => function($query){
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) - SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END)) - SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)  as current_quantity
            '))
            ->groupBy('product_id');
        }])->get();
    }
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
    public function edit(string $id)
    {
        return Product::findOrFail($id);
    }
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
    public function users(){
        return User::select('user_id','username')->get();
    }

    public function productsbyUser($id)
    {
        return Product::with(['transactions' => function($query) use ($id){
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) - SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END)) - SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)  as current_quantity
            '))
            ->groupBy('product_id')
            ->where('user_id',$id);
        }])
        
        ->get();
    }
    public function productsbyUserWithRange($id, $from, $to)
    {
        $Datefrom = Carbon::create($from)->format('Y-m-d');
        $Dateto = Carbon::create($to)->format('Y-m-d');
        return Product::with(['transactions' => function($query) use ($Datefrom, $Dateto, $id) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) - SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END)) - SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)  as current_quantity
            '))
            ->groupBy('product_id')
            ->whereBetween('created_at',[$Datefrom,$Dateto])
            ->where('user_id',$id);
        }])
        ->get();
    }

    public function productsWithRange($id, $from, $to)
    {
        $Datefrom = Carbon::create($from)->format('Y-m-d');
        $Dateto = Carbon::create($to)->format('Y-m-d');
        return Product::with(['transactions' => function($query) use ($Datefrom, $Dateto) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) - SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END)) - SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)  as current_quantity
            '))
            ->groupBy('product_id')->whereBetween('created_at',[$Datefrom,$Dateto]);
        }])
        ->get();
    }
}
