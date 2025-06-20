<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BranchOffices;
use App\Models\Countries;
use App\Models\Commissions;
use App\Models\Clients;
use App\Models\ClientsPhones;
use App\Models\ClientsPassportHistory;
use App\Models\ClientsVisaHistory;
use App\Models\Passport;
use App\Models\Process;
use App\Models\ProcessStatus;
use App\Models\ProcessDocuments;
use App\Models\Sales;
use App\Models\SalesBilling;
use App\Models\SalesBranchOffice;
use App\Models\SalesClients;
use App\Models\SalesCommissions;
use App\Models\SalesDetails;
use App\Models\SalesPayment;
use App\Models\SalesProcess;
use App\Models\SalesStatus;
use App\Models\SalesUsers;
use App\Models\Visas;
use App\Models\User;

class SalesController extends Controller
{
    public function info($id){
        $sale = Sales::join('branch_office', 'branch_office.id', 'sales.branch_offices_id')
                    ->join('sales_process', 'sales_process.sales_id', 'sales.id')
                    ->join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                    ->join('sales_status', 'sales_status.sales_id', 'sales.id')
                    ->select(
                        'sales.id', 'branch_office.name as branch_office', 'sales.folio', 'sales.date', 'sales.total', 'sales.type',
                        'sales_process.advance_payment', 'sales_process.payable',
                        'sales_billing.email', 'sales_billing.names', 'sales_billing.lastname1', 'sales_billing.lastname2',
                        'sales_billing.phone', 'sales_status.status'
                    )
                    ->where('sales_status.is_last', true)
                    ->where('sales.id', $id)
                    ->first();

        $clients = SalesClients::join('clients', 'clients.id', 'sales_clients.clients_id')
                            ->join('process', 'process.clients_id', 'clients.id')
                            ->leftJoin('process_details', 'process_details.process_id', 'process.id')
                            ->leftJoin('sales_process_account', 'sales_process_account.clients_id', 'clients.id')
                            ->select(
                                'clients.id as clients_id', 'clients.names', 'clients.lastname1', 'clients.lastname2', 'clients.curp', 
                                'process.id as process_id', 'process.type', 'process.subtype', 'process.age_type', 'process.option_type', 'process.visa_type', 'process.complete',
                                'process_details.clave_ds_160 as ds_160',
                                'sales_process_account.email'
                            )
                            ->where('sales_clients.sales_id', $id)
                            ->get();
        
        foreach($clients as $client){
            $client['schedule'] = \DB::table('schedule_details')
                                        ->where('clients_id', $client->clients_id)
                                        ->select('office', 'appointment_date', 'schedule', 'status', 'observations')
                                        ->get();

        }

        $sale['clients'] = $clients;

        return response()->json([
            'success' => true,
            'data' => $sale
        ]);
    }

    /** send procedure email to client */
    public function sendWelcomeEmail($sales_id, $type){
        $sale = Sales::join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                    ->select('sales.date', 'sales.folio', 'sales_billing.email', 'sales_billing.names', 'sales_billing.lastname1', 'sales_billing.lastname2')
                    ->where('sales.id', $sales_id)
                    ->first();

        $date = date_create($sale->date);
        $sale->date = date_format($date, 'd/m/Y');

        $salesClients = SalesClients::join('clients', 'clients.id', 'sales_clients.clients_id')
                                ->join('process', 'process.clients_id', 'clients.id')
                                ->select(
                                    'clients.names', 'clients.lastname1', 'clients.lastname2', 
                                    'process.type', 'process.age_type', 'process.subtype', 'process.option_type', 'process.visa_type'
                                )
                                ->where('sales_clients.sales_id', $sales_id)
                                ->where('process.type', $type)
                                ->get();


        $documents = ProcessDocuments::select('documents')->where('type', $type)->first();

        $user = SalesUsers::join('users', 'users.id', 'sales_users.users_id')
                        ->where('sales_id', $sales_id)
                        ->select('email', 'names', 'lastname1', 'lastname2')
                        ->first();

        $procedure = [
            'sale' => $sale,
            'clients' => $salesClients,
            'documents' => $documents,
            'user' => $user
        ];

        $data = [
            'body' => $procedure,
            'sender' => 'postmaster@visas-premier.com',
            'subject' => 'Inicio de trámite',
            'receiver' => $sale->email
        ];

        $mail['data'] = $data;

        $route = 'emails.sales.register';

        $email = \Mail::send($route, $mail, function($m) use($data){
            $m->from($data['sender'], 'Visas premier');
            
            $receiverName = $data['body']['sale']['names'].' '.$data['body']['sale']['lastname1'].(is_null($data['body']['sale']['lastname2']) ? '' : ' '.$data['body']['sale']['lastname2']);

            $m->to($data['receiver'], $receiverName)->subject($data['subject']);
        });

        return 'ok';
    }

    /** visa procedure list */
    public function visa_list(){
        $visas = Sales::join('sales_clients', 'sales_clients.sales_id', 'sales.id')
                        ->join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                        ->join('sales_status', 'sales_status.sales_id', 'sales.id')
                        ->selectRaw('
                            sales.id, sales.folio, sales.date,
                            COUNT(sales_clients.id) AS no_applicants,
                            sales_billing.names, sales_billing.lastname1, sales_billing.lastname2, sales_billing.email, sales_billing.phone,
                            sales_status.status
                        ')
                        ->where('sales_status.is_last', true)
                        ->groupBy(
                            'sales.id', 'sales.folio', 'sales.date', 'sales_billing.names', 'sales_billing.lastname1', 'sales_billing.lastname2', 
                            'sales_billing.email', 'sales_billing.phone', 'sales_status.status'
                            )
                        ->get();

        $list = array();

        foreach($visas as $visa){
            $list[] = [
                'id' => $visa->id, 'folio' => $visa->folio, 'date' => $visa->date, 'no_applicants' => $visa->no_applicants,
                'client' => $visa->names.' '.$visa->lastname1.(is_null($visa->lastname2) ? '' : ' '.$visa->lastname2),
                'email' => $visa->email, 'phone'=>$visa->phone, 'status' => $visa->status
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $list
        ]);
    }

    /** save visa procedure */
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
                    $salesUsers = new SalesUsers();
                    $salesUsers->sales_id = $sales->id;
                    $salesUsers->users_id = $commissionItem['users_id'];
                    $salesUsers->save();                    
                }      
                
                /** save sales commisions */
                $commission = Commissions::join('users_commissions', 'users_commissions.commissions_id', 'commissions.id')
                                    ->select('commissions.concept', 'users_commissions.amount')
                                    ->where('commissions.concept', $commissionItem['commission'])
                                    ->where('users_commissions.users_id', $commissionItem['users_id'])
                                    ->first();

                if($commission){
                    $saleCommissions = new SalesCommissions();
                    $saleCommissions->sales_id = $sales->id;
                    $saleCommissions->users_id = $commissionItem['users_id'];
                    $saleCommissions->concept = $commission->concept;
                    $saleCommissions->amount = $commission->amount;
                    $saleCommissions->save();
                    
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
            SalesBranchOffice::create([
                'sales_id' => $sales->id,
                'name' => $branchOffice->name
            ]);

            /** sales payment */
            $payment = $request->payment;
            SalesPayment::create([
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
            SalesBilling::create([
                'sales_id' => $sales->id,
                'email' => $billing['email'],
                'names' => $billing['names'],
                'lastname1' => $billing['lastname1'],
                'lastname2' => $billing['lastname2'],
                'phone' => $billing['phone']
            ]);


            /** create user account  */
            $user = new User();
            $user->names =  $billing['names'];
            $user->lastname1 =  $billing['lastname1'];
            $user->lastname2 =  $billing['lastname2'];
            $user->email =  stryolower($billing['names'].'.'.$billing['lastname1'].'.'.\Str::random(4).'@visas-premier.com');
            $user->password =  bcrypt('@cc350.T3mp.2025');
            $user->role = 'cliente';
            $user->save();

            $sales_user = new SalesUsers();
            $sales_user->sales_id = $sales->id;
            $sales_user->users_id = $user->id;
            $sales_user->save();

            /** sales status */
            SalesStatus::create([
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
                    $clientPhones = new ClientsPhones();
                    $clientPhones->clients_id = $client->id;
                    $clientPhones->type = $phone['type'];
                    $clientPhones->number = $phone['number'];
                    $clientPhones->save();
                    
                }

                /** sales clients */
                SalesClients::create([
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
                ProcessStatus::create([
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
                $findPassport = ClientsPassportHistory::where('clients_id', $client->id)
                                    ->where('number', $passport->number)
                                    ->first();

                if(!isset($findPassport)){
                    ClientsPassportHistory::create([
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
                    $findVisa = ClientsVisaHistory::where('clients_id', $client->id)
                                    ->where('number', $passport->number)
                                    ->first();

                    if(!isset($findVisa)){
                        ClientsVisaHistory::create([
                            'clients_id' => $client->id,
                            'number' => $visa->number,
                            'expedition_date' => $visa->expedition_date,
                            'expiration_date' => $visa->expiration_date
                        ]);
                    }
                }

            }

            /** send email */
            $this->sendWelcomeEmail($sales->id, 'Visa');

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

    public function sendAccessVisaForm(Request $request){
        $this->sendWelcomeEmail(2, 'Visa');
        /*
        $user = SalesUsers::join('users', 'users.id', 'sales_users.users_id')
                    ->select('users.email', 'users.names', 'users.lastname1', 'users.lastname2')
                    ->where('sales_users.sales_id', $request->sales_id)
                    ->first();         

        $sale = [
            'user_name' => $user->names.' '.$user->lastname1.(is_null($user->lastname2) ? '' : ' '.$user->lastname2 ),
            'user_email' => $user->email
        ];

         $data = [
            'body' => $sale,
            'sender' => 'postmaster@visas-premier.com',
            'subject' => 'Formulario - detalles de visa',
            'receiver' => $user->email
        ];*/
    }
}

