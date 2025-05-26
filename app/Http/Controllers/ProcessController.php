<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ProcessDetails;
use App\Models\SalesProcessAccount;

class ProcessController extends Controller
{
    public function updateDS160(Request $request){
        try{
            \DB::beginTransaction();

            $details = $request->all();

            foreach($details as $detail){
                $process = ProcessDetails::where('process_id', $detail['process_id'])->first();

                if(!isset($process)){
                    $process = new ProcessDetails();
                    $process->process_id = $detail['process_id'];
                }
                
                $process->clave_ds_160 = $detail['ds_160'];
                $process->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Se actualizo el registro correctamente'
            ]);

        }catch(\Exception $ex){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$ex->getMessage().' '.$ex->getLine()
            ]);
        }
    }

     public function processAccountUpdate(Request $request){
        try{
            \DB::beginTransaction();

            $clients = $request->all();

            foreach($clients as $client){
                $account = SalesProcessAccount::where('clients_id', $client['clients_id'])->first();

                if(!isset($account)){
                    $account = new SalesProcessAccount();
                    $account->sales_id = $client['sales_id'];
                    $account->clients_id = $client['clients_id'];
                }

                $account->email = $client['user'];
                $account->password = $client['password'];
                $account->save();
            }


            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Los registros se guardaron correctamente'
            ]);

        }catch(\Exception $ex){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error, '.$ex->getMessage().' '.$ex->getLine()
            ]);
        }
    }
}
