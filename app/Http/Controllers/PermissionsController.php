<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Permissions;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function permissionsAssigned($id){

    	$role_permissions = \DB::table('role_has_permissions')
                        ->join('permissions', function($j){
							$j->on('permissions.id', '=', 'role_has_permissions.permission_id');
						})
    					->select('permissions.id as id', 'permissions.name', 'permissions.display_name')
    					->where('role_id',$id)
    					->orderBy('permission_id', 'ASC')->get();

    	$permissions = Permissions::select('id', 'name', 'display_name')->orderBy('id', 'ASC')->get();
    
    	$availables = array();
        $assigned = array();
    	foreach ($permissions as $p) {
    		$existe = array_search($p->id, array_column($role_permissions->toArray(), 'id'));
            
    		if(strlen($existe) == 0){
    			$availables[] = array(
                                'id' => $p->id, 
                                'name'=>$p->name, 
                                'display_name'=>$p->display_name, 
                            );
    		}else{
    			$assigned[] = array(
                                'id' => $p->id, 
                                'name'=>$p->name, 
                                'display_name'=>$p->display_name, 
                            );
    		}
    	}

    	return response()->json([
            'success'=>true,
            'message'=>'',
            'data'=>[
                'availables'=>$availables,
                'assigned'=>$assigned
            ]            
        ]);
    }

    public function assign(Request $request){
        try{
            $rol_id 		= $request->role_id;
            $permission 	= $request->permission_name;
            
            $rol = Role::find($rol_id);

            $user = User::where('role', $rol->name)->get();

            foreach($user as $u){
                $u->givePermissionTo($permission);
            }
            
            $rol->givePermissionTo($permission);

            return response()->json('ok');

        }catch(\Exception $e){
            return response()->json(['success'=>false, 'message'=>$e->getMessage().' - '.$e->getLine()]);
        }
    	
    }

    public function design(Request $request){
        try{
            $rol_id 		= $request->role_id;
            $permission 	= $request->permission_name;

            $rol = Role::find($rol_id);
            $user = User::where('role', $rol->name)->get();

            foreach($user as $u){
                $u->revokePermissionTo($permission);
            }

            $rol->revokePermissionTo($permission);

            return response()->json('ok');

        }catch(\Exception $e){
            return response()->json(['success'=>false, 'message'=>$e->getMessage()]);
        }
    }

    public function fullList(){
        $permissions = Permissions::select('id', 'name', 'display_name', 'description')->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $permissions
        ]);
    }
}
