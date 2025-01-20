<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BranchOffices;
use App\Models\Countries;
use App\Models\Commissions;
use App\Models\Clients;
use App\Models\Passport;
use App\Models\Process;
use App\Models\Sales;
use App\Models\SalesDetails;
use App\Models\SalesProcess;
use App\Models\Visas;

class SalesController extends Controller
{

    public function visa_list(){
        $visas = Sales::join('sales_clients', 'sales_clients.sales_id', 'sales.id')
                        ->join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                        ->join('sales_status', 'sales_status.sales_id', 'sales.id')
                        ->selectRaw('
                            sales.id, sales.folio, sales.date,
                            COUNT(sales_clients.id) AS no_applicants,
                            sales_billing.names, sales_billing.lastname1, sales_billing.lastname2, sales_billing.email,
                            sales_status.status
                        ')
                        ->groupBy('sales.id', 'sales.folio', 'sales.date', 'sales_billing.names', 'sales_billing.lastname1', 'sales_billing.lastname2', 'sales_billing.email', 'sales_status.status')
                        ->get();

        $list = array();

        foreach($visas as $visa){
            $list[] = [
                'id' => $visa->id, 'folio' => $visa->folio, 'date' => $visa->date, 'no_applicants' => $visa->no_applicants,
                'client' => $visa->names.' '.$visa->lastname1.(is_null($visa->lastname2) ? '' : ' '.$visa->lastname2),
                'email' => $visa->email, 'status' => $visa->status
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $list
        ]);
    }

    public function visa_store(Request $request){
        try{
            \DB::beginTransaction();

            $sale = $request->sales;

            /** register sales */
            $sales = new Sales();
            $sales->branch_offices_id = $sale['branch_office_id'];
            $sales->date = $sale['date'];
            $sales->folio = $sale['folio'];
            $sales->total = $sale['total'];
            $sales->type = $sale['type'];
            $sales->save();

            $commissions = $request->commissions;

            foreach($commissions as $commissionItem){
                if($commissionItem['type'] == 'seller'){
                    /** save sales users */
                    $salesUsers = \DB::table('sales_users')->insert([
                        'sales_id' => $sales->id,
                        'users_id' => $commissionItem['users_id']
                    ]);
                }      
                
                /** save sales commisions */
                $commission = Commissions::join('users_commissions', 'users_commissions.commissions_id', 'commissions.id')
                                    ->select('commissions.concept', 'users_commissions.amount')
                                    ->where('commissions.concept', $commissionItem['commission'])
                                    ->where('users_commissions.users_id', $commissionItem['users_id'])
                                    ->first();

                if($commission){
                    $saleCommissions = \DB::table('sales_commissions')->insert([
                        'sales_id'=>$sales->id,
                        'users_id' => $commissionItem['users_id'],
                        'concept' => $commission->concept,
                        'amount' => $commission->amount
                    ]);
                }
                    
            }

            /** sale details */
            $details = $request->details;

            foreach($details as $detailItem){
                $salesDetails = new SalesDetails();
                $salesDetails->sales_id = $sales->id;
                $salesDetails->sales_concepts_id = $detailItem['sales_concepts_id'];
                $salesDetails->amount = $detailItem['amount'];
                $salesDetails->save();
            }

            /** sales branch office - if branch office is deleted */
            $branchOffice = BranchOffices::where('id', $sales->branch_offices_id)->first();
            \DB::table('sales_branch_office')->insert([
                'sales_id' => $sales->id,
                'name' => $branchOffice->name
            ]);

            /** sales payment */
            $payment = $request->payment;
            \DB::table('sales_payment')->insert([
                'sales_id' => $sales->id,
                'method_payment' => $payment['method_payment'],
                'amount' => $payment['amount'],
                'status' => 'Pagado',
                'platform' => $payment['platform'],
                'receipt' => $payment['receipt'],
                'reference' => $payment['receipt'] ? $payment['receipt'] : null
            ]);

            /**sales billing */
            $billing = $request->billing;
            \DB::table('sales_billing')->insert([
                'sales_id' => $sales->id,
                'email' => $billing['email'],
                'names' => $billing['names'],
                'lastname1' => $billing['lastname1'],
                'lastname2' => $billing['lastname2'],
                'phone' => $billing['phone']
            ]);

            /** sales status */
            \DB::table('sales_status')->insert([
                'sales_id' => $sales->id,
                'status' => 'Ficha pendiente',
                'is_last' => true
            ]);
            
            /** sales process */
            $process = $request->sale_process;

            $sales_process = new SalesProcess();
            $sales_process->sales_id = $sales->id;
            $sales_process->status = 'Incompleto'; //se debe de verificar si el total de sales es igual al total de la suma de process
            $sales_process->advance_payment = $process['advance_payment'];
            $sales_process->payable = $process['payable']; //es el resto que falta por pagar
            $sales_process->contact = $process['contact'];
            $sales_process->save();
            
            /** clients */
            $clients = $request->clients;
            foreach($clients as $item){
                $client = new Clients();
                $client->names = $item['client']['names'];
                $client->lastname1 = $item['client']['lastname1'];
                $client->lastname2 = $item['client']['lastname2'];
                $client->curp = $item['client']['curp'];
                $client->birthdate = $item['client']['birthdate'];
                $client->city = $item['client']['city'];
                $client->country_birth_id = $item['client']['country_birth_id'];
                $client->state_birth_id = $item['client']['state_birth_id'];
                $client->save();

                /** clients phones */
                $phones = $item['phones'];
                foreach($phones as $phone){
                    \DB::table('clients_phones')->insert([
                        'clients_id' => $client->id,
                        'type' => $phone['type'],
                        'number' => $phone['number']
                    ]);
                }

                /** sales clients */
                \DB::table('sales_clients')->insert([
                    'sales_id' => $sales->id,
                    'clients_id' => $client->id
                ]);
                
                /** process */
                $process = new Process();
                $process->clients_id = $client->id;
                $process->type = $item['process']['type'];
                $process->subtype = $item['process']['subtype'];
                $process->age_type = $item['process']['age_type'];
                $process->option_type = $item['process']['option_type'];
                $process->visa_type = $item['process']['visa_type'];
                $process->save();

                /** process status */
                \DB::table('process_status')->insert([
                    'process_id' => $process->id,
                    'status' => 'Inicio',
                    'is_last' => true
                ]);

                /** passport */
                $passport = new Passport();
                $passport->process_id = $process->id;
                $passport->number = $item['passport']['number'];
                $passport->expedition_date = $item['passport']['expedition_date'];
                $passport->expiration_date = $item['passport']['expiration_date'];
                $passport->save();

                /** client passport history */
                $findPassport = \DB::table('clients_passport_history')->where('clients_id', $client->id)
                                    ->where('number', $passport->number)
                                    ->first();

                if(!isset($findPassport)){
                    \DB::table('clients_passport_history')->insert([
                        'clients_id' => $client->id,
                        'number' => $passport->number,
                        'expedition_date' => $passport->expedition_date,
                        'expiration_date' => $passport->expiration_date
                    ]);
                }

                /** visa */
                if(isset($item['visa'])){
                    $visa = new Visas();
                    $visa->process_id = $process->id;
                    $visa->number = $item['visa']['number'];
                    $visa->expedition_date = $item['visa']['expedition_date'];
                    $visa->expiration_date = $item['visa']['expiration_date'];
                    $visa->expedition_city = $item['visa']['expedition_city'];
                    $visa->save();

                    /** client visa  history */
                    $findVisa = \DB::table('clients_visa_history')->where('clients_id', $client->id)
                                    ->where('number', $passport->number)
                                    ->first();

                    if(!isset($findVisa)){
                        \DB::table('clients_visa_history')->insert([
                            'clients_id' => $client->id,
                            'number' => $visa->number,
                            'expedition_date' => $visa->expedition_date,
                            'expiration_date' => $visa->expiration_date
                        ]);
                    }
                }

            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro de visa se realizao correctamente'
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
