<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use App\Models\User;

class RolesController extends Controller
{
    public function delete($id){
        try{
            \DB::beginTransaction();

            $role = Role::find($id);

            $users = User::role($role->name)->get();

            if(count($users) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "El rol tiene usuarios asignados"
                ]);
            }

            $role->delete();

            \DB::commit();

            return response()->json([
                'success'=>true,
                'mensaje'=>"Rol ".$role->name." se elimino con exito."
            ]);
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json([
                'success'=>false,
                'message'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()
            ]);            
        }
    }

    public function edit($id){
        $role = Role::select('display_name', 'description')->where('id', $id)->first();

        return response()->json([
            'success'=>true,
            'message'=>'',
            'data'=>$role
        ]);
    }

    public function fullList(){
        $roles = Role::select('id', 'display_name as name', 'description')->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $roles
        ]);
    }

    public function store(Request $request){
        try{
            \DB::beginTransaction();

            $rol = new Role();
            $rol->name = $request->name;
            $rol->display_name = $request->display_name;
            $rol->description = $request->description;
            $rol->guard_name = 'web';
            $rol->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "El rol se registro correctamente"
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' - line '.$e->getLine()
            ]);
        }
    }

    public function update($id, Request $request){
        try{
            \DB::beginTransaction();

            $rol = Role::find($id);
            $rol->name = $request->name;
            $rol->display_name = $request->display_name;
            $rol->description = $request->description;
            $rol->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "El rol se actualizo correctamente"
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' - line '.$e->getLine()
            ]);
        }
    }
}
