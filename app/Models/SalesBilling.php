<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesBilling extends Model
{
    protected $table = 'sales_billing';
    protected $fillable = [
        'sales_id',
        'email',
        'names',
        'lastname1',
        'lastname2',
        'phone'
    ];
}
