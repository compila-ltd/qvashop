<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitcoinWallet extends Model
{
    use HasFactory;

    // Fillable data
    protected $fillable = [
        'combined_order_id',
        'invoice_id',
        'invoice',
        'token',
        'btc_amount'
    ];
}
