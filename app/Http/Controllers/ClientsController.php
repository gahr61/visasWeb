<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesVisasPayment;

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
}
