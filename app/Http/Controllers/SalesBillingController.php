<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesBilling;
use App\Models\SalesStatus;

class SalesBillingController extends Controller
{
    public function sendVisaPayment(Request $request){
        try{
            \DB::beginTransaction();
            
            $sales_id = $request->sales_id;
            $option = $request->option;

            $lastSaleStatus = SalesStatus::where('sales_id', $sales_id)->where('is_last', true)->first();

            if($lastSaleStatus->status == 'Ficha pendiente'){
                $lastSaleStatus->is_last = false;
                $lastSaleStatus->save();

                $saleStatus = new SalesStatus();
                $saleStatus->sales_id = $sales_id;
                $saleStatus->status = 'Con ficha';
                $saleStatus->is_last = true;
                $saleStatus->save();
            }
            
            if($option == 'send'){
                $sale = SalesBilling::join('sales', 'sales.id', 'sales_billing.sales_id')
                                ->where('sales_billing.sales_id', $sales_id)
                                ->select('sales.folio', 'sales_billing.email', 'sales_billing.names', 'sales_billing.lastname1', 'sales_billing.lastname2')
                                ->first();

                $fullname = $sale->names.' '.$sale->lastname1.(is_null($sale->lastname2) ? '' : ' '.$sale->lastname2);

                $files = $request->file('files');

                $linkToken = (new GeneralController)->encriptString($sale->folio);

                $data = [
                    'body' => [
                        'client' => [
                            'email' => $sale->email
                        ],
                        'sale'=>[
                            'folio' => $sale->folio,
                            'fullName' => $fullname
                        ],
                        //'linkToken' => $linkToken,
                        'procedure_type' => 'Visa'
                    ],
                    'sender' => 'postmaster@visas-premier.com',
                    'subject' => 'Ficha de pago - Tráamite de visa',
                    'receiver' => $sale->email
                ];

                $mail['data'] = $data;
                $route = 'emails.sales.visa_payment';

                \Mail::send($route, $mail, function($m) use($data, $files){
                    $m->from($data['sender'], 'Visas premier');
                    $m->to($data['receiver'], $data['body']['client']['email'])->subject($data['subject']);
                    
                    foreach($files as $file){
                        $m->attach($file, [
                            'as' => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType()
                        ]);
                    }
                });
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => $option == 'send' ? 'La ficha se envio correctamente' : 'La ficha se entrego correctamente'
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
