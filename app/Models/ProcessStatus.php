<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessStatus extends Model
{
    protected $table = 'process_status';
    protected $fillable = [
        'process_id',
        'status',
        'is_last'
    ];
}
