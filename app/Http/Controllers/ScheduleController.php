<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleDetails;

class ScheduleController extends Controller
{
    public function update(Request $request){
        try {
            \DB::beginTransaction();

            $clients = $request->all();

            foreach($clients as $client){
                if($client['consulado'] != ''){
                    $consulado = ScheduleDetails::where('sales_id', $client['sales_id'])
                                                ->where('clients_id', $client['clients_id'])
                                                ->where('office', 'Consulado')
                                                ->first();

                    if(is_null($consulado)){
                        $consulado = new ScheduleDetails();
                        $consulado->sales_id = $client['sales_id'];
                        $consulado->clients_id = $client['clients_id'];
                        $consulado->office = 'Consulado';
                    }                    
                    
                    $consulado->appointment_date = $client['consulado'];                    
                    $consulado->schedule = $client['consulado_time'];
                    $consulado->save();
                }

                if($client['cas'] != ''){
                    $consulado = ScheduleDetails::where('sales_id', $client['sales_id'])
                                                ->where('clients_id', $client['clients_id'])
                                                ->where('office', 'CAS')
                                                ->first();

                    if(!isset($consulado)){
                        $consulado = new ScheduleDetails();
                        $consulado->sales_id = $client['sales_id'];
                        $consulado->clients_id = $client['clients_id'];
                        $consulado->office = 'CAS';
                    }

                    $consulado->appointment_date = $client['cas'];
                    $consulado->schedule = $client['cas_time'];
                    $consulado->save();
                }                
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Los datos se guardaron correctamente'
            ]);

        } catch (\Exception $ex) {
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error, '.$ex->getMessage().' '.$ex->getLine()
            ]);
        }
    }
}
