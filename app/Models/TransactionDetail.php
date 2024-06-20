<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $table = 'transaction_details';
    protected $primaryKey = 'details_id';
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'remarks',
        'user_id'
    ];
}
