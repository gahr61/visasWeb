<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Commissions;
use App\Models\UserCommissions;

class CommissionsController extends Controller
{
    public function list(){
        $commissions = Commissions::selectRaw('id, concept')->get();

        return response()->json([
            'success'=>true,
            'message'=>'',
            'data'=>$commissions
        ]);
    }

    public function userUpdate(Request $request){
        try{
            \DB::beginTransaction();

            UserCommissions::where('users_id', $request->users_id)->delete();

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
                $userCommission->users_id = $request->users_id;
                $userCommission->amount = $item['amount'];
                $userCommission->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Las comisiones se actualizaron correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }
}
