<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ClientAddress;
use App\Models\ClientsOccupation;
use App\Models\ClientsParents;
use App\Models\ClientsPhones;
use App\Models\ClientsStudies;
use App\Models\ClientsVisaHistory;
use App\Models\Passport;
use App\Models\Process;
use App\Models\ProcessDetails;
use App\Models\ProcessHistory;
use App\Models\Residence;
use App\Models\SalesClients;
use App\Models\SalesProcessAccount;

class ProceduresController extends Controller
{
    public function infoVisasDetails($id){
        $clients = SalesClients::join('clients', 'clients.id', 'sales_clients.clients_id')                                                
                        ->select(
                            'clients.id', 'clients.names', 'clients.lastname1', 'clients.lastname2', 'clients.curp', 'clients.birthdate', 'clients.sex', 'clients.city', 'clients.ine', 'clients.civil_status', 
                            'clients.country_birth_id', 'clients.state_birth_id', 'clients.city_birth','clients.nationality',                            
                        )
                        ->where('sales_clients.sales_id', $id)
                        ->get();
        $data = [];

        foreach($clients as $client){
            $client['process'] = Process::select('id', 'observations')->where('clients_id', $client->id)
                                            ->orderBy('id', 'desc')->first();

            $client['account'] = SalesProcessAccount::where('clients_id', $client->id)
                                                ->select('id', 'email', 'password')
                                                ->orderBy('id', 'desc')
                                                ->first();

            $process_id = $client->process->id;

            $client['passport'] = Passport::select(
                                            'id','number', 'expedition_date', 'expiration_date',
                                            'expedition_countries_id', 'expedition_states_id', 'expedition_city',
                                            'states_id'
                                        )
                                        ->where('process_id', $process_id)
                                        ->orderBy('id', 'desc')
                                        ->first();

            $client['address'] = ClientAddress::where('clients_id', $client->id)
                                        ->select(
                                            'id', 'street', 'int_number', 'ext_number', 'postal_code', 'colony',
                                            'city', 'countries_id', 'states_id'
                                        )
                                        ->orderBy('id', 'desc')
                                        ->first();
                                        
            $client['phones'] = ClientsPhones::where('clients_id', $client->id)
                                        ->select('id', 'type', 'number')
                                        ->get();

            $client['parents'] = ClientsParents::where('clients_id', $client->id)
                                        ->select(
                                            'id', 'relationship', 'full_name', 'birthdate', 'has_visa'
                                        )
                                        ->get();

            $client['occupation'] = ClientsOccupation::where('clients_id', $client->id)
                                        ->select(
                                            'id', 'occupations_id', 'name', 'address', 'salary', 'antiquity'
                                        )
                                        ->orderBy('id', 'desc')
                                        ->first();
            
            $client['studies'] = ClientsStudies::where('clients_id', $client->id)
                                        ->select('id','name', 'address', 'period')
                                        ->get();

            $client['process_details'] = ProcessDetails::select(
                                                    'id', 'travel_date', 'address_eeuu', 'travel_date_eeuu',
                                                    'time_stay_eeuu', 'travel_reason', 'cover_expenses', 'has_visit_eeuu',
                                                    'date_visit_eeuu', 'time_visit_eeuu', 'clave_ds_160', 
                                                    'travel_before', 'travel_before_countries', 'observations'
                                                )
                                                ->where('process_id', $process_id)
                                                ->orderBy('id', 'desc')
                                                ->first();

            $client['process_history'] = ProcessHistory::where('process_id', $process_id)
                                                ->select('id', 'has_tried_visa', 'date')
                                                ->orderBy('id', 'desc')
                                                ->first();

            $client['visa_history'] = ClientsVisaHistory::where('clients_id', $client->id)
                                                ->select('id', 'number', 'expedition_date', 'expiration_date')
                                                ->orderBy('id', 'desc')->first();

            $client['residence'] = Residence::where('process_id', $process_id)
                                            ->select(
                                                'id', 'full_name', 'cel_phone', 'work_phone', 'personal_phone'
                                            )
                                            ->orderBy('id', 'desc')
                                            ->first();
            
                    
        }

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }
}
