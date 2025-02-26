<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesConfirmToken;
use App\Models\SalesVisasPayment;

class SalesTokenController extends Controller
{
    public function generateToken($sales_id){
        try{
            \DB::beginTransaction();

            \DB::table('sales_confirm_token')->where('sales_id', $sales_id)->delete();

            $token = bin2hex(random_bytes(32));

            $expiresAt = now()->addDays(5);

            $salesConfirm = new SalesConfirmToken();
            $salesConfirm->sales_id = $sales_id;
            $salesConfirm->token = $token;
            $salesConfirm->expires_at = $expiresAt;
            $salesConfirm->save();

            \DB::commit();

            return $token;
        }catch(\Exception $e){
            \DB::rollback();

            return 'error';
        }
        
    }

    public function verifyToken(Request $request){
        $token = $request->token;

        // Buscar el token en la base de datos
        $storedToken = SalesConfirmToken::where('token', $token)->select('sales_id', 'token', 'expires_at')->first();

        if ($storedToken) {
            // Verificar si el token ha expirado
            if (now()->greaterThan($storedToken->expires_at)) {
                return response()->json(['success' => false, 'message' => 'El token ha expirado'], 400);
            }

            // Token válido

            // get sale_visa_payment data
            $salesPayment = SalesVisasPayment::join('clients', 'clients.id', 'sales_visas_payment.clients_id')
                                            ->select(
                                                'sales_visas_payment.id', 'sales_visas_payment.ticket', 'sales_visas_payment.is_confirmed',
                                                'sales_visas_payment.sales_id', 'sales_visas_payment.clients_id',
                                                'clients.names', 'clients.lastname1', 'clients.lastname2'
                                            )
                                            ->where('sales_id', $storedToken->sales_id)
                                            ->get();
            
            $totalConfirmed = 0;
            
            foreach($salesPayment as $sale){
                if($sale->is_confirmed){
                    //$totalConfirmed++;
                }
            }

            if($totalConfirmed == count($salesPayment)){
                return response()->json([
                    'success' => false,
                    'message' => 'Ya se ha realizado la confirmación, sera redirigido a la pagina principal',
                    'data' => [
                        'canShow' => false
                    ]
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Token válido. Acceso permitido',
                'data'    => $salesPayment
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Token no válido'], 400);
    }


}
