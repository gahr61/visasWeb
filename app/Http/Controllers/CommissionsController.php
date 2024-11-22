<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Commissions;

class CommissionsController extends Controller
{
    public function list(){
        $commissions = Commissions::selectRaw('id as value, concept as label')->get();

        return response()->json([
            'success'=>true,
            'message'=>'',
            'data'=>$commissions
        ]);
    }
}
