<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesClients extends Model
{
    protected $fillable = ['sales_id', 'clients_id'];
    public $timestamps = false;
}
