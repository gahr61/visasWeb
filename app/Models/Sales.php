<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'branch_offce_id',
        'date',
        'folio',
        'total',
        'is_familiar'
    ];

    public function salesClients(){
        return $this->hasMany(salesClients::class);
    }

    public function clients(){
        return $this->hasManyThrough(Clients::class, SalesClients::class);
    }
}
