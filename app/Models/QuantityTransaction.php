<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuantityTransaction extends Model
{
    use HasFactory;
    protected $table = 'quantity_transaction';
    protected $primaryKey = 'change_quantity_id';
    protected $fillable = [
        'user_id',
        'remarks'
    ];
}
