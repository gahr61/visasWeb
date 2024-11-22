<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BranchOffices;

class BranchOfficeController extends Controller
{
    public function list(){
        $branch_office = BranchOffice::select('id', 'name')->get();

        return response()->json($branch_office);
    }
}
