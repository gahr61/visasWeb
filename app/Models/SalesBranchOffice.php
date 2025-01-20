<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesBranchOffice extends Model
{
    protected $table = 'sales_branch_office';
    protected $fillable = ['sales_id', 'name'];
    public $timestamps = false;
}
