<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Remarks;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::all();
    }

    public function fetchAllUserTransactions()
    {
        return Transaction::where('user_id',Auth::user()->user_id)->get();
    }
    public function store($remarks ,Request $request)
    {
        $quantity_transactions = Transaction::create([
            'user_id' => Auth::user()->user_id
        ]);
        foreach($request->all() as $key => $item){
            TransactionDetail::create([
                 'transaction_id' => $quantity_transactions->transaction_id,
                 'product_id' => $item['product_id'],
                 'quantity' => $item['quantity'],
                 'remarks' => $remarks,
                 'user_id' => Auth::user()->user_id
             ]);
         }

         return response()->json([
             'status' => 'transaction save successfully!'
         ]);
    }

    public function fetchAllProducts()
    {
        return Product::All();
    }
    public function fetchAllRemarks()
    {
        return Remarks::All();
    }

    public function edit(string $id)
    {
        return TransactionDetail::where('transaction_id',$id)->get();
    }

    public function update(string $id, Request $request, $remarks)
    {
        TransactionDetail::where('transaction_id', $id)->delete();

        foreach($request->all() as $key => $item){
            TransactionDetail::create([
                 'transaction_id' => $id,
                 'product_id' => $item['product_id'],
                 'quantity' => $item['quantity'],
                 'remarks' => $remarks,
                 'user_id' => Auth::user()->user_id
             ]);
         }
         return response()->json([
            'status' => 'transaction details updated successfully!'
        ]);
    }

    public function destroy(string $id)
    {
        Transaction::destroy($id);
        TransactionDetail::where('transaction_id', $id)->delete();

        return response()->json([
            'status' => 'Transaction deleted successfully!'
        ]);
    }

}
