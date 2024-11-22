<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Countries;
use App\Models\Passport;
use App\Models\Process;
use App\Models\Sales;
use App\Models\SalesDetails;
use App\Models\SalesProcess;
use App\Models\Visas;

class SalesController extends Controller
{
    public function store(Request $request){
        try{
            \DB::beginTransaction();

            $sale = $request->sales;

            /** register sales */
            $sales = new Sales();
            $sales->branch_office_id = $sale['branch_office_id'];
            $sales->date = $sale['date'];
            $sales->folio = $sale['folio'];
            $sales->total = $sale['total'];
            $sales->is_familiar = $sale['is_familiar'];
            $sales->save();


            // register sales process
            $sales_process = new SalesProcess();
            $sales_process->sales_id = $sales->id;
            $sales_process->status = 'Incompleto'; //se debe de verificar si el total de sales es igual al total de la suma de process
            $sales_process->advance_payment = $sale['advace_payment'];
            $sales_process->payable = $sale['payable']; //es el resto que falta por pagar
            $sales_process->contact = $sale['contact'];
            $sales_process->save();

            // guarda sales detauls
            $details = $request->saleDetails;
            foreach($details as $details){
                $sales_details = new SalesDetauls();
                $sales_details->sales_concept_id = $detail['sales_concept_id'];
                $sales_details->amount = $detail['amount'];
                $sales_details->save();
            }
            

            // guarda process
            $processes = $request->process;

            foreach($processes as $item){
                $process = new Process();
                $process->sales_id = $sales->id;
                $process->type = $item['type'];
                $process->subtype = $item['subtype'];
                $process->age_type = $item['age_type'];
                $process->option_type = $item['option_type'];
            
                if(isset($item['visa_type'])){
                    $process->visa_type = $item['visa_type'];
                }

                $process->save();

                if(isset($item['passport'])){
                    $dataPassport = $item['passport'];

                    $passport = new Passport();
                    $passport->process_id = $process->id;
                    $passport->countries_id = $dataPassport['countries_id'];
                    $passport->number = $dataPassport['number'];
                    $passport->expedition_date = $dataPassport['expedition_date'];
                    $passport->expiration_date = $dataPassport['expiration_Date'];
                    $passport->save();
                }

                if(isset($item['visa'])){
                    $dataVisa = $item['visa'];

                    $visa = new Visas();
                    $visa->process_id = $process->id;
                    $visa->countries_id = $dataVisa['countries_id'];
                    $visa->number = $dataVisa['number'];
                    $visa->expedition_date = $dataVisa['expedition_date'];
                    $visa->expiration_date = $dataVisa['expiration_Date'];
                    $visa->save();

                }
                

                

            }




            \DB::commit();

        }catch(\Exception $e){
            \DB::rollback();
        }
    }
}
