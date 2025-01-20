<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientsPassportHistory extends Model
{
    protected $table = 'clients_passport_history';
    protected $fillable = [
        'clients_id', 'number', 'expedition_date', 'expiration_date'
    ];
    public $timestamps = false;
}
