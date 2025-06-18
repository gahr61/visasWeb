<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use App\Models\Commissions;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserCommissions;
use App\Models\SalesVisasPayment;
use App\Models\SalesStatus;

class UsersController extends Controller
{
    public function edit($id){
        $user = User::join('users_details', 'users_details.users_id', 'users.id')                    
                    ->select(
                        'users.id', 'users.names', 'users.lastname1', 'users.lastname2', 'users.email', 'users.role',
                        'users_details.goal', 'users_details.salary'
                    )->where('users.id', $id)->first();
        
        $role = Role::where('name', $user->role)->first();

        $user->role_text = $user->role;
        $user->role = $role->id;
        

        $user['commissions'] = Commissions::join('users_commissions', 'users_commissions.commissions_id', 'commissions.id')
                                ->select('commissions.id', 'commissions.concept', 'users_commissions.amount')
                                ->where('users_commissions.users_id', $user->id)
                                ->get();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function delete($id){
        try{
            \DB::beginTransaction();

            $user = User::find($id);

            \DB::table('users_commissions')->where('users_id', $id)->delete();

            $user->delete();

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'El usuario re elimino correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function list(){
        $users = User::join('users_details', 'users_details.users_id', 'users.id')
                    ->select(
                        'users.id', 'users.names', 'users.lastname1', 'users.lastname2', 'users.email','users.role',
                        'users_details.goal', 'users_details.salary'
                    )->where('users.role', '!=', 'cliente')
                    ->get();

        $list = [];
        foreach($users as $user){
            $role = Role::select('display_name')->where('name', $user->role)->first();

            $user->role = $role->display_name;

            $commissions = Commissions::leftJoin('users_commissions', 'users_commissions.commissions_id', 'commissions.id')
                                        ->select('commissions.id', 'commissions.concept', 'users_commissions.amount')
                                        ->where('users_commissions.users_id', $user->id)
                                        ->get();

            $totalCommissions = 0;

            foreach($commissions as $commission){
                $totalCommissions += $commission->amount;
            }

            $list[] = [
                'id'=>$user->id,
                'fullName' => $user->names.' '.$user->lastname1.(is_null($user->lastname2) ? '' : ' '.$user->lastname2),
                'email' => $user->email,
                'role' => $user->role,
                'goal' => $user->goal,
                'salary' => $user->salary,
                'commissions' => $commissions,
                'totalCommission' => $totalCommissions
            ];
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $list
        ]);

    }

    /**
     * get list users except system admin
     */
    public function sales(){
        $users = User::select('id', 'names', 'lastname1', 'lastname2')
                        ->where('role', '!=', 'administrador_de_sistema')
                        ->get();

        $list = [];

        foreach($users as $user){
            $list[] = [
                'value'=>$user->id,
                'label'=>$user->names.' '.$user->lastname1.(is_null($user->lastname2) ? '' : ' '.$user->lastname2)
            ];
        }

        return response()->json([
            'success'=>true,
            'data'=>$list
        ]);
    }

    public function store(Request $request){
        try{
            \DB::beginTransaction();

            $user = new User();
            $user->fill($request->all());
            $user->password = bcrypt($request->password);
            $user->save();

            $details = new UserDetails();
            $details->users_id = $user->id;
            $details->goal = $request->goal;
            $details->salary = $request->salary;
            $details->save();

            foreach($request->commissions as $item){
                $commissionId = $item['id'];

                if(is_null($commissionId)){
                    $commission = new Commissions();
                    $commission->concept = $item['concept'];
                    $commission->save();

                    $commissionId = $commission->id;
                }

                $userCommission = new UserCommissions();
                $userCommission->commissions_id = $commissionId;
                $userCommission->users_id = $user->id;
                $userCommission->amount = $item['amount'];
                $userCommission->save();
            }

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'El usuario se registro correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' - '.$e->getLine()
            ]);
        }
    }

    public function userConfirmVisaPayment(Request $request){
        try{
            \DB::beginTransaction();

            $files = $request->file('files');

            if(count($files) == 0){
                return response()->josn(['success' => false, 'message' => 'Los archivos son requeridos']);
            }

            $correctFiles = 0;
            $savedFile = '';

            $sales_id = $request->sales_id;
            $clients_id = $request->clients_id;

            foreach($files as $file){
                $fileName = 'file_'.time().rand(0, 10000);
                $extension = $file->getClientOriginalExtension();

                $fullnameFile = $fileName.'.'.$extension;

                $filePath = 'sales/'.$sales_id.'/clients/'.$clients_id;

                $salesVisaPayment = new SalesVisasPayment();
                $salesVisaPayment->sales_id = $sales_id;
                $salesVisaPayment->clients_id = $clients_id;
                $salesVisaPayment->ticket = $filePath.'/'.$fullnameFile;
                $salesVisaPayment->is_confirmed = true;
                $salesVisaPayment->confirmed_by = 'User';
                $salesVisaPayment->save();

                if($extension == 'pdf'){
                    $savedFile = (new GeneralController)->saveFileOnStorage($file, $filePath, $fullnameFile);
                }else{
                    $savedFile = (new GeneralController)->saveImageOnStorage($file, $filePath, $fullnameFile);
                }
                

                if($savedFile == 'saved'){
                    $correctFiles++;
                }
            }

            $lastSaleStatus = SalesStatus::where('sales_id', $sales_id)->where('is_last', true)->first();

            if($lastSaleStatus->status == 'Con ficha'){
                $lastSaleStatus->is_last = false;
                $lastSaleStatus->save();

                $saleStatus = new SalesStatus();
                $saleStatus->sales_id = $sales_id;
                $saleStatus->status = 'Ficha pagada';
                $saleStatus->is_last = true;
                $saleStatus->save();
            }

            if(count($files) == $correctFiles){
                \DB::commit();

                return response()->json(['success' => true, 'message' => 'Confirmación exitosa']);
            }

            return response()->json(['success' => false, 'message' => 'error'.$correctFiles.' '.$savedFile]);

        }catch(\Exception $e){
            \DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    /**
     * Allows to reset password of logged in user or specific user
     * @param \Illuminate\Http\Request $request [user_id?, password, password_confirmation]
     * @return \Illuminate\Http\JsonResponse
     */
    public function restorePassword(Request $request){
        try{
            \DB::beginTransaction();

            $request->validate([
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required'
            ]);

            $user_id = auth()->user()->id;
            $changeRequired = false;

            if(!is_null($request->users_id)){
                $user_id = $request->users_id;
                $changeRequired = true;
            }

            $user = User::find($user_id);
            $user->password = bcrypt($request->password);
            $user->change_password_required = $changeRequired;
            $user->save();

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message' => 'La contraseña se actualizo correctamente'
            ], 200);
        }catch(\Exception $e){
            \DB::rollback();
            
            return response()->json([
                'stauts' => 'error',
                'message' => $e->getMessage().' '.$e->getCode().' '.$e->getLine().' in file UsersController'
            ], 500);
        }
    }

    public function update($id, Request $request){
        try{
            \DB::beginTransaction();

            $user = User::find($id);
            $user->fill($request->all());
            $user->save();

            $details = UserDetails::where('users_id', $id)->first();
            $details->goal = $request->goal;
            $details->salary = $request->salary;
            $details->save();

            //delete user commissions 
            \DB::table('users_commissions')->where('users_id', $id)->delete();

            foreach($request->commissions as $item){
                $commissionId = $item['id'];

                if(is_null($commissionId)){
                    $commission = new Commissions();
                    $commission->concept = $item['concept'];
                    $commission->save();

                    $commissionId = $commission->id;
                }

                $userCommission = new UserCommissions();
                $userCommission->commissions_id = $commissionId;
                $userCommission->users_id = $user->id;
                $userCommission->amount = $item['amount'];
                $userCommission->save();
            }

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'El usuario se actualizo correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
