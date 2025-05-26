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
                $relationship = new ClientsParents();
                $relationship->clients_id = $id;
                $relationship->relationship = $item['relationship'];
                $relationship->full_name = $item['fullname'];
                $relationship->birthdate = $item['birthdate'];
                $relationship->has_visa = $item['has_visa'];
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
     * update or save clients occupation
     */
    public function clientsSaveUpdateOccupation(Request $request){
        try{
            \DB::beginTransaction();

            $occupations_id = $request->occupations_id;

            if($request->otherOccupation != ''){
                $occupation = new Occupations();
                $occupation->name = $request->otherOccupation;
                $occupation->save();

                $occupations_id = $occupation->id;
            }


            $clientOccupation = ClientsOccupation::where('clients_id', $request->clients_id)->first();

            if(!isset($clientOccupation)){
                $clientOccupation = new ClientsOccupation();
                $clientOccupation->clients_id = $request->clients_id;
            }

            $clientOccupation->name = $request->name;
            $clientOccupation->occupations_id = $occupations_id;
            $clientOccupation->address = $request->address;
            $clientOccupation->salary = $request->salary;
            $clientOccupation->antiquity = $request->antiquity;
            $clientOccupation->save();

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
                $school->address = $item['address'];
                $school->period = $item['period'];
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

    public function clientsSaveTravel(Request $request){
        try{
            \DB::beginTransaction();

            //save residence
            $itemResidence = $request->residence;

            $residence = Residence::find($itemResidence['id']);

            if(!isset($residence)){
                $residence = new Residence();
            }

            $residence->process_id = $itemResidence['process_id'];
            $residence->full_name = $itemResidence['full_name'];
            $residence->cel_phone = $itemResidence['cel_phone'];
            $residence->work_phone = $itemResidence['work_phone'];
            $residence->personal_phone = $itemResidence['personal_phone'];
            $residence->save();


            //save process details
            $process =$request->process_details;

            $processDetails = ProcessDetails::find($process->id);

            if(!isset($processDetails)){
                $processDetails = new ProcessDetails();
            }

            $processDetails->process_id = $process['id'];
            $processDetails->travel_date = $process['travel_date'];
            $processDetails->address_eeuu = $process['address_eeuu'];
            $processDetails->travel_date_eeuu = $process['travel_date_eeuu'];
            $processDetails->time_stay_eeuu = $process['time_stay_eeuu'];
            $processDetails->travel_reason = $process['travel_reason'];
            $processDetails->cover_expenses = $process['cover_expenses'];
            $processDetails->has_visit_eeuu = $process['has_visit_eeuu'];
            $processDetails->date_visit_eeuu = $process['date_visit_eeuu'];
            $processDetails->time_visit_eeuu = $process['time_visit_eeuu'];
            $processDetails->travel_before = $process['travel_before'];
            $processDetails->travel_before_countries = $process['travel_before_countries'];
            $processDetails->save();

            /**
             * save process_history 
             */
            $history = $request->history;

            $processHistory = ProcessHistory::find($history['id']);

            if(!isset($processHistory)){
                $processHistory = new ProcessHistory();                
            }

            $processHistory->process_id = $process['id'];
            $processHistory->has_tried_visa = $history['has_tried_visa'];
            $processHistory->date = $history['date'];
            $processHistory->observations = $history['observations'];
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
                $visa = Visas::find($visaData['id']);
                
                if(!isset($visa)){
                    $visa = new Visas();                    
                }
                $visa->process_id = $process['id'];
                $visa->number = $visaData['number'];
                $visa->expedition_date = $visaData['expedition_date'];
                $visa->expiration_date = $visaData['expiration_date'];
                $visa->sav();

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

            \DB::commit();

            return response()->json([
                'message' => true,
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

}
