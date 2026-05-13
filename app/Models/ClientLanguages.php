<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientLanguages extends Model
{
    protected $table = 'client_languages';
    protected $fillable = ['clients_id', 'language', 'percentage'];
}
