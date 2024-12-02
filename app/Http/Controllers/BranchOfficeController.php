<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BranchOffices;

class BranchOfficeController extends Controller
{

    public function delete($id){
        try{
            \DB::beginTransaction();

            $branchOffice = BranchOffices::find($id);
            $branchOffice->delete();

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message' => 'La sucursal se elimino correctamente'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success'=>true,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function edit($id){
        $branchOffice = BranchOffices::find($id);

        \DB::commit();

        return response()->json([
            'success'=>true,
            'data' => $branchOffice
        ]);
    }

    public function list(){
        $branch_office = BranchOffices::select('id', 'name', 'location')->get();

        return response()->json([
            'success'=>true,
            'data'=>$branch_office
        ]);
    }

    public function store(Request $request){
        try{
            \DB::beginTransaction();

            $branchOffice = new BranchOffices();
            $branchOffice->name = $request->name;
            $branchOffice->location = $request->location;
            $branchOffice->save();

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message' => 'La sucursal se registro correctamente'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success'=>false,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function update($id, Request $request){
        try{
            \DB::beginTransaction();

            $branchOffice = BranchOffices::findOrFail($id);
            $branchOffice->name = $request->name;
            $branchOffice->location = $request->location;
            $branchOffice->save();

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message' => 'La sucursal se actualizo correctamente'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success'=>true,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }
}
