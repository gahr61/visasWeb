<?php

namespace App\Http\Controllers;

use App\Models\RelationshipType;

class RelationshipTypesController extends Controller
{
    public function list(){
        $relationships = RelationshipType::select('id', 'name')->orderBy('name', 'ASC')->get();

        return response()->json([
            'success' => true,
            'data' => $relationships
        ]);
    }
}
