<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Commissions;
use App\Models\User;

class UsersController extends Controller
{
    public function list(){
        $users = User::join('users_details', 'users_details.users_id', 'users.id')
                    ->select(
                        'users.id', 'users.names', 'users.lastname1', 'users.lastname2', 'users.email', 'users.role',
                        'users_details.goal', 'users_details.salary'
                    )
                    ->get();

        $list = [];
        foreach($users as $user){
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
                'status'=>'success',
                'message' => 'Password was updated successfully'
            ], 200);
        }catch(\Exception $e){
            \DB::rollback();
            
            return response()->json([
                'stauts' => 'error',
                'message' => $e->getMessage().' '.$e->getCode().' '.$e->getLine().' in file UsersController'
            ], 500);
        }
    }
}
