<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Occupations;
use App\Models\ClientsOccupation;

class OccupationsController extends Controller
{
    public function list(){
        $occupations = Occupations::select('id', 'name')->orderBy('name', 'ASC')->get();

        return response()->json([
            'success' => true,
            'data' => $occupations
        ]);
    }

    public function store(Request $request){
        try{
            \DB::beginTransaction();

            $occupation = new Occupations();
            $occupation->name = $request->name;
            $occupation->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro se guardo correctamente',
                'data' => $occupation->id
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function edit($id){
        $occupation = Occupations::select('id', 'name')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $occupation
        ]);
    }

    public function update($id, Request $request){
        try{
            \DB::beginTransaction();

            $occupation = Occupations::find($id);
            $occupation->name = $request->name;
            $occupation->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro se actualizo correctamente',
                'data' => $occupation->id
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function destroy($id){
        try{
            \DB::beginTransaction();

            //search if client has occupation
            $clients = ClientsOccupation::where('occupations_id', $id)->get();

            if(count($clients) > 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Mo es posible eliminar el registro ya que se encuentra asignado a '.count($clients).' clientes'
                ]);
            }

            $occupation = Occupations::where('id', $id);
            $occupation->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro se elimino correctamente',                
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }
}
