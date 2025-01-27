<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesBilling;
use App\Models\SalesClients;
use App\Models\SalesStatus;
use App\Models\SalesVisasPayment;

class SalesBillingController extends Controller
{
    /**
     * Send visa payment to client email
     */
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

    /** 
     * get visa payment client list
    */
    public function visaPaymentList($id){
        $salesClients = SalesClients::join('clients', 'clients.id', 'sales_clients.clients_id')
                                ->leftJoin('sales_visas_payment', 'sales_visas_payment.clients_id', 'clients.id')
                                ->select(
                                    'sales_visas_payment.id', 'clients.names', 'clients.lastname1', 'clients.lastname2',
                                    'sales_visas_payment.ticket', 'sales_visas_payment.is_confirmed'
                                )
                                ->where('sales_clients.sales_id', $id)
                                ->get();
        $canConfirm = true;
        $confirmed = 0;
        foreach($salesClients as $sale){
            if($sale->is_confirmed){
                $confirmed++;
            }
        }

        if(count($salesClients) == $confirmed){
            $canConfirm = false;
        }
        

        return response()->json([
            'success' => true,
            'data' => [
                'salesClients' => $salesClients,
                'canConfirm' => $canConfirm
            ]
        ]);
                                
    }

    /**
     * update sales visa payment 
     */
    public function visaPaymentUpdate(Request $request){
        try{
            \DB::beginTransaction();

            $salesPayment = $request->salesPayment;
            $sales_id = '';

            foreach($salesPayment as $item){
                $salesVisasPayment = SalesVisasPayment::where('id', $item['id'])->where('is_confirmed', false)->first();
                $salesVisasPayment->is_confirmed = $item['is_confirmed'];
                $salesVisasPayment->save();

                $sales_id = $salesVisasPayment->sales_id;
            }

            $confirmPayments = SalesVisasPayment::where('sales_id', $sales_id)
                                        ->where('is_confirmed', true)
                                        ->get();

            if(count($salesPayment) == count($confirmPayments)){
                $salesStatus = SalesStatus::where('sales_id', $sales_id)->where('is_last', true)->first();
                $salesStatus->is_last = false;
                $salesStatus->save();

                $newSalesStatus = new SalesStatus();
                $newSalesStatus->sales_id = $sales_id;
                $newSalesStatus->status = 'Ficha pagada';
                $newSalesStatus->is_last = true;
                $newSalesStatus->save();

            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Se actualizo el estatus de pago de visas'
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
