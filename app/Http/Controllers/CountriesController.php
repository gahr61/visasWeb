<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Countries;
use App\Models\States;

class CountriesController extends Controller
{
    public function countries(){
        $countries = Countries::select('id as value', 'name as label')->get();

        return response()->json(['success' => true, 'data' => $countries]);
    }

    public function statesByCountry($id){
        $states = States::select('id as value', 'name as label')
                        ->where('countries_id', $id)
                        ->get();

        return response()->json(['success' => true, 'data' => $states]);
    }
}
