<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'cashier_id', 'customer_name', 'total', 'receipt', 'outlet_id'
    ];

    public function transactionDetail() {
        return $this->hasMany(TransactiontDetail::class);  
    }
}