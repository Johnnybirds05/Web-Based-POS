<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    protected $fillable = [
        'user_id',
        'remarks'
    ];

    public function transaction_details(){
        $this->hasMany(TransactionDetail::class, 'transaction_id','transaction_id');
    }
}
