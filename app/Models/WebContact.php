<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebContact extends Model
{
    protected $table = 'web_contact';
    protected $fillable = ['name', 'email', 'phone', 'message'];
}
