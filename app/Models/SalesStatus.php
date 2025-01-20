<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesStatus extends Model
{
    protected $table = 'sales_status';
    protected $fillable = [
        'sales_id',
        'status',
        'is_last'
    ];
}
