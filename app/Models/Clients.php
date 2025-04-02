<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{

    public function process(){
        return $this->hasMany(Process::class);
    }

    public function salesClients(){
        return $this->hasMany(SalesClients::class);
    }
}
