<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ClientAddress;
use App\Models\Clients;
use App\Models\ClientsPhones;
use App\Models\ClientsParents;
use App\Models\SalesVisasPayment;
use App\Models\Occupations;
use App\Models\ClientsOccupation;
use App\Models\ClientsStudies;
use App\Models\Residence;
use App\Models\ProcessDetails;
use App\Models\Visas;
use App\Models\ProcessHistory;
use App\Models\ClientsVisaHistory;
use App\Models\SalesProcessAccount;
use App\Models\Passport;
use App\Models\ClientsSocialNetwork;
use App\Models\ClientLanguages;
use App\Models\RelationshipType;
use App\Models\Process;

class ClientsController extends Controller
{
    /**
     * Clients confirm visa from web
     */
    public function clientsConfirmVisaPayment(Request $request){
        try{
            \DB::beginTransaction();

            $images = $request->file('images');

            if(!isset($images)){
                return response()->json([
                    'success' => false, 
                    'message' => 'Las imagenes son requeridas'
                ]);
            }

            $imagesCorrect = 0;
            $savedImage = '';

            foreach($images as $image){
                $image_name = 'image_'.time().rand(0, 10000);
                $fullNameImage = $image_name.'.png';

                $sales_id = $request->sales_id;
                $clients_id = $request->clients_id;

                $paymentPath = 'sales/'.$sales_id.'/clients/'.$clients_id;

                $salesVisaPayment = new SalesVisasPayment();
                $salesVisaPayment->sales_id = $request->sales_id;
                $salesVisaPayment->clients_id = $request->clients_id;
                $salesVisaPayment->ticket = $paymentPath.'/'.$fullNameImage;
                $salesVisaPayment->is_confirmed = false;
                $salesVisaPayment->confirmed_by = 'Client';
                $salesVisaPayment->save();
                

                $savedImage = (new GeneralController)->saveImageOnStorage($image, $paymentPath, $fullNameImage);

                if($savedImage == 'saved'){
                    $imagesCorrect++;
                }
            }

            if(count($images) == $imagesCorrect){
                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Confirmación exitosa'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $savedImage
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    /**
     * Update clients info
     */
    public function clientsUpdate($id, Request $request){
        try{
            \DB::beginTransaction();

            $client = Clients::findOrFail($id);
            $client->names = $request->names;
            $client->lastname1 = $request->lastname1;
            $client->lastname2 = $request->lastname2;
            $client->birthdate = $request->birthdate;
            $client->sex = $request->sex;
            $client->curp = $request->curp;
            $client->civil_status = $request->civil_status;
            $client->country_birth_id = $request->country_birth_id;
            $client->state_birth_id = $request->state_birth_id;
            $client->city_birth = $request->city_birth;
            $client->nationality = $request->nationality;
            $client->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El cliente se actualizo correctamente'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    /**
     * update client address
     */
    public function clientsUpdateAddress(Request $request){
        try{
            \DB::beginTransaction();

            $address = ClientAddress::find($request->id);
            if(is_null($request->id)){
                $address = new ClientAddress();

                $address->clients_id = $request->address['clients_id'];
            }

            
            $address->street = $request->address['street'];
            $address->int_number = $request->address['int_number'];
            $address->ext_number = $request->address['ext_number'];
            $address->postal_code = $request->address['postal_code'];
            $address->colony = $request->address['colony'];
            $address->city = $request->address['city'];
            $address->countries_id = $request->address['countries_id'];
            $address->states_id = $request->address['states_id'];
            $address->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La dirección se actualizo correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error . '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    /**
     * update client phones
     */
    public function clientsUpdatePhones(Request $request){
        try{
            \DB::beginTransaction();

            ClientsPhones::where('clients_id', $request->clients_id)->delete();

            $phones = $request->phones;

            foreach($phones as $item){
                $phone = new ClientsPhones();
                $phone->clients_id =  $request->clients_id;
                $phone->type = $item['type'];
                $phone->number = $item['number'];
                $phone->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Los teléfonos se actualizaron correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error . '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    /**
     * save or update relationships
     */
    public function clientsRelationships(Request $request){
        try{
            \DB::beginTransaction();

            $id = $request->clients_id;

            ClientsParents::where('clients_id', $id)->delete();

            foreach($request->relationships as $item){
                RelationshipType::firstOrCreate(['name' => $item['relationship']]);

                $relationship = new ClientsParents();
                $relationship->clients_id = $id;
                $relationship->relationship = $item['relationship'];
                $relationship->full_name = $item['fullname'];
                $relationship->birthdate = $item['birthdate'];
                $relationship->has_visa = $item['has_visa'];
                $relationship->lives_in_usa = $item['lives_in_usa'] ?? false;
                $relationship->country_birth_id = $item['country_birth_id'] ?? null;
                $relationship->state_birth_id = $item['state_birth_id'] ?? null;
                $relationship->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro se guardo correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error .'.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    /**
     * save clients occupations (list, one may be flagged as primary)
     */
    public function clientsSaveUpdateOccupation(Request $request){
        try{
            \DB::beginTransaction();

            $clients_id = $request->clients_id;
            $occupations = $request->occupations ?? [];

            ClientsOccupation::where('clients_id', $clients_id)->delete();

            $primaryIndex = collect($occupations)->search(fn($item) => !empty($item['is_primary']));

            foreach($occupations as $index => $item){
                $occupations_id = $item['occupations_id'] ?? null;

                if(!empty($item['otherOccupation'])){
                    $occupation = new Occupations();
                    $occupation->name = $item['otherOccupation'];
                    $occupation->save();

                    $occupations_id = $occupation->id;
                }

                $clientOccupation = new ClientsOccupation();
                $clientOccupation->clients_id = $clients_id;
                $clientOccupation->name = $item['name'] ?? null;
                $clientOccupation->occupations_id = $occupations_id;
                $clientOccupation->is_primary = $primaryIndex !== false ? $index === $primaryIndex : $index === 0;
                $clientOccupation->salary = $item['salary'] ?? null;
                $clientOccupation->antiquity = $item['antiquity'] ?? null;
                $clientOccupation->hire_date = $item['hire_date'] ?? null;
                $clientOccupation->activities_description = $item['activities_description'] ?? null;

                if(isset($item['address'])){
                    $clientOccupation->street = $item['address']['street'] ?? null;
                    $clientOccupation->int_number = $item['address']['int_number'] ?? null;
                    $clientOccupation->ext_number = $item['address']['ext_number'] ?? null;
                    $clientOccupation->postal_code = $item['address']['postal_code'] ?? null;
                    $clientOccupation->colony = $item['address']['colony'] ?? null;
                    $clientOccupation->city = $item['address']['city'] ?? null;
                    $clientOccupation->countries_id = $item['address']['countries_id'] ?? null;
                    $clientOccupation->states_id = $item['address']['states_id'] ?? null;
                }

                $clientOccupation->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Los datos de ocupación se registraron correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    /**
     * save client schools
     */
    public function clientsSaveSchools(Request $request){
        try{
            \DB::beginTransaction();

            $clients_id = $request->clients_id;

            ClientsStudies::where('clients_id', $clients_id)->delete();

            foreach($request->schools as $item){
                $school = new ClientsStudies();
                $school->clients_id = $clients_id;
                $school->name = $item['name'];
                $school->degree_level = $item['degree_level'] ?? null;
                $school->is_last_degree = $item['is_last_degree'] ?? false;
                $school->period = $item['period'];
                $school->street = $item['address']['street'] ?? null;
                $school->int_number = $item['address']['int_number'] ?? null;
                $school->ext_number = $item['address']['ext_number'] ?? null;
                $school->postal_code = $item['address']['postal_code'] ?? null;
                $school->colony = $item['address']['colony'] ?? null;
                $school->city = $item['address']['city'] ?? null;
                $school->countries_id = $item['address']['countries_id'] ?? null;
                $school->states_id = $item['address']['states_id'] ?? null;
                $school->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Los datos de ocupación se registraron correctamente'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function clientsSaveLanguages(Request $request){
        try{
            \DB::beginTransaction();

            $clients_id = $request->clients_id;

            ClientLanguages::where('clients_id', $clients_id)->delete();

            foreach($request->languages as $item){
                $language = new ClientLanguages();
                $language->clients_id = $clients_id;
                $language->language = $item['language'];
                $language->percentage = $item['percentage'];
                $language->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Los idiomas se guardaron correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }
    }

    public function clientsSaveTravel(Request $request){
        try{
            \DB::beginTransaction();

            //save residence
            $itemResidence = $request->residence;

            if(isset($itemResidence['id'])){
                $residence = Residence::find($itemResidence['id']);

                if(!isset($residence)){
                    $residence = new Residence();
                }
            }else{
                $residence = new Residence();
            }
            
            if(!empty($itemResidence['relationship'])){
                RelationshipType::firstOrCreate(['name' => $itemResidence['relationship']]);
            }

            $residence->process_id = $itemResidence['process_id'];
            $residence->full_name = $itemResidence['full_name'];
            $residence->relationship = $itemResidence['relationship'] ?? null;
            $residence->citizen_status = ($itemResidence['citizen_status'] ?? null) ?: null;
            $residence->cel_phone = $itemResidence['cel_phone'];
            $residence->work_phone = $itemResidence['work_phone'] ?? null;
            $residence->personal_phone = $itemResidence['personal_phone'] ?? null;
            $residence->save();


            //save process details
            $process =$request->process_details;

            $processDetails = ProcessDetails::find($process['id'] ?? null);

            if(!isset($processDetails)){
                $processDetails = new ProcessDetails();
            }

            $processDetails->process_id = $itemResidence['process_id'];
            $processDetails->travel_date = $process['travel_date'] ?? $process['travel_date_eeuu'];
            $processDetails->street_eeuu = $process['address_eeuu']['street'] ?? null;
            $processDetails->number_eeuu = $process['address_eeuu']['number'] ?? null;
            $processDetails->city_eeuu = $process['address_eeuu']['city'] ?? null;
            $processDetails->state_eeuu = ($process['address_eeuu']['state'] ?? null) ?: null;
            $processDetails->zip_eeuu = $process['address_eeuu']['zip'] ?? null;
            $processDetails->travel_date_eeuu = $process['travel_date_eeuu'] ?? null;
            $processDetails->time_stay_eeuu = $process['time_stay_eeuu'] ?? null;
            $processDetails->travel_reason = $process['travel_reason'] ?? null;
            $processDetails->cover_expenses = $process['cover_expenses'] ?? null;
            $processDetails->has_visit_eeuu = (bool) ($process['has_visit_eeuu'] ?? false);
            $processDetails->date_visit_eeuu = ($process['date_visit_eeuu'] ?? null) ?: null;
            $processDetails->time_visit_eeuu = $process['time_visit_eeuu'] ?? null;
            $processDetails->travel_before = (bool) ($process['travel_before'] ?? false);
            $processDetails->travel_before_countries = $process['travel_before_countries'] ?? null;
            $processDetails->visa_approved = (bool) ($process['visa_approved'] ?? false);
            $processDetails->travel_countries = $process['travel_countries'] ?? null;
            $processDetails->save();

            /**
             * save process_history 
             */
            $history = $request->history;

            if(isset($history['id'])){
                $processHistory = ProcessHistory::find($history['id']);
            }
            
            if(!isset($processHistory)){
                $processHistory = new ProcessHistory();                
            }

            $processHistory->process_id = $itemResidence['process_id'];
            $processHistory->has_tried_visa = (bool) ($history['has_tried_visa'] ?? false);
            $processHistory->date = ($history['date'] ?? null) ?: null;
            $processHistory->observations = $history['observations'] ?? "";
            $processHistory->save();

            /**
             * find visa data
             * if existe update
             * if not create
             * 
             * save and update clients_visa_history
             */
            $visaData = $request->visa;

            if($visaData){
                if(isset($visa['id'])){
                    $visa = Visas::find($visaData['id']);
                }

                if(!isset($visa)){
                    $visa = new Visas();                    
                }
                $visa->process_id = $itemResidence['process_id'];
                $visa->number = $visaData['number'];
                $visa->expedition_date = $visaData['expedition_date'];
                $visa->expiration_date = $visaData['expiration_date'];
                $visa->save();

                $clientVisa = ClientsVisaHistory::where('clients_id', $request->clients_id)
                                    ->where('number', $visaData['number'])
                                    ->first();

                if(!isset($clientVisa)){
                    $clientVisa = new ClientsVisaHistory();
                    $clientVisa->clients_id = $request->clients_id;
                    $clientVisa->number = $visaData['number'];
                    $clientVisa->expedition_date = $visaData['expedition_date'];
                    $clientVisa->expiration_date = $visaData['expiration_date'];
                    $clientVisa->save();
                }
            }

            $networks = $request->social_networks;

            foreach($networks as $item){
                $social = ClientsSocialNetwork::where('id', $item['id'])->first();

                if(!isset($social)){
                    $social = new ClientsSocialNetwork();
                }

                $social->clients_id = $item['clients_id'];
                $social->network = $item['socialNetwork'];
                $social->username = $item['username'];
                $social->email = $item['email'];
                $social->save();
            }

            if($request->has('mark_complete')){
                Process::where('id', $itemResidence['process_id'])->update([
                    'complete' => (bool) $request->mark_complete
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Datos generales de viaje se registraron correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error. '.$e->getMessage().' '.$e->getLine()
            ]);
        }

    }

    /**
     * autocomplete search clients
     */
    public function clientsAutocomplete(Request $request){

        $query = $request->input('query');

        if (!$query) {
            return response()->json([]);
        }

        $results = Clients::where(function ($q) use ($query) {
            $q->where('names', 'like', "%$query%")
              ->orWhere('lastname1', 'like', "%$query%")
              ->orWhere('lastname2', 'like', "%$query%")
              ->orWhere('curp', 'like', "%$query%");
        })
        ->select(
            'id', 'names', 'lastname1', 'lastname2', 'curp'
        )
        ->limit(10)
        ->get();

        return response()->json(['success' => true, 'data'=>$results]);
    }

    /**
     * get values from client to load into visas form
     */
    public function clientsData($id){
        $clients = Clients::where('id', $id)
                        ->select(
                            'clients.id', 'clients.names', 'clients.lastname1', 'clients.lastname2', 'clients.curp',
                            'clients.birthdate', 'clients.country_birth_id as country', 'clients.state_birth_id as state', 'clients.city_birth as city'
                        )
                        ->first();

        $clients['phone'] = ClientsPhones::where('clients_id', $id)
                                ->where('type', 'Celular')
                                ->select('id', 'number')
                                ->first();

        $passport = Passport::join('process', 'process.id', 'passport.process_id')
                            ->where('process.clients_id', $id)
                            ->select('passport.id', 'passport.number', 'passport.expedition_date', 'passport.expiration_date')
                            ->orderBy('passport.id', 'DESC')
                            ->first();

        $clients['passport'] = $passport;

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }

}
