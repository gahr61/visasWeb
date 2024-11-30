<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Commissions;
use App\Models\UserCommissions;

class CommissionsController extends Controller
{
    public function delete($id){
        try{
            \DB::beginTransaction();

            UserCommissions::where('commissions_id', $id)->delete();

            $commission = Commissions::find($id);
            $commission->delete();

            \DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'La comisión se elimino correctamente'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }
    public function edit($id){
        $commission = Commissions::select('id', 'concept')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $commission
        ]);
    }

    public function list(){
        $commissions = Commissions::selectRaw('id, concept')->get();

        return response()->json([
            'success'=>true,
            'message'=>'',
            'data'=>$commissions
        ]);
    }

    public function store(Request $request){
        try{
            \DB::beginTransaction();

            $commission = new Commissions();
            $commission->concept = $request->concept;
            $commission->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La comisión se registro correctamente'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function update(Request $request){
        try{
            \DB::beginTransaction();

            $commission = Commissions::find($request->id);
            $commission->concept = $request->concept;
            $commission->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La comisión se actualizo correctamente'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
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

    public function verifyCommissionUser($id){
        $commissionsUser = UserCommissions::where('commissions_id', $id)->get();

        if(count($commissionsUser) > 0){
            return response()->json([
                'success' => true,
                'data' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => false
        ]);
    }
}
