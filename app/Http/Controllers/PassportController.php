<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Passport;

class PassportController extends Controller
{
     /**
     * Update passport data
     */
    public function update($id, Request $request){
        try{
            \DB::beginTransaction();

            $passport = Passport::findOrFail($id);
            $passport->number = $request->number;
            $passport->expedition_date = $request->expedition_date;
            $passport->expiration_date = $request->expiration_date;
            $passport->expedition_countries_id = $request->expedition_countries_id;
            $passport->expedition_states_id = $request->expedition_states_id;
            $passport->expedition_city = $request->expedition_city;
            $passport->save();


            /** 
             * update passport history
             */

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El pasaporte se actualizo correctamente'
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
