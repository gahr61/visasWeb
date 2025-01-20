<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientsVisaHistory extends Model
{
    protected $table = 'clients_visa_history';
    protected $fillable = [
        'clients_id', 'number', 'expedition_date', 'expiration_date'
    ];
}
