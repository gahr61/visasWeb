<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    protected $table = 'sales_payment';
    protected $fillable = [
        'sales_id',
        'method_payment',
        'amount',
        'status',
        'platform',
        'receipt',
        'reference'
    ];
}
