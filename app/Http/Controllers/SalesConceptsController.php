<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesConcepts;

class SalesConceptsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $catalog = SalesConcepts::select('id', 'name', 'price')->get();

        return response()->json([
            'success' => true,
            'data' => $catalog
        ]);
    }

    public function history($id){
        $catalogs = \DB::table('sales_concepts_history')
                        ->select('price', 'change_date as date')
                        ->where('sales_concepts_id', $id)
                        ->orderBy('id', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $catalogs
        ]);
    }

    public function visaPrices(){
        $history = SalesConcepts::where('name', 'LIKE', '%Visa%')
                        ->where('is_process', true)
                        ->select('id as value', 'name as label', 'price')
                        ->get();

        return response()->json(['success' => true, 'data' => $history]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            \DB::beginTransaction();

            $concept = new SalesConcepts();
            $concept->name = $request->name;
            $concept->price = $request->price;
            $concept->is_process = $request->is_process;
            $concept->save();

            \DB::table('sales_concepts_history')->insert([
                'sales_concepts_id' => $concept->id,
                'price' => $concept->price,
                'change_date' => date('Y-m-d')
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El elemento se guardo correctamente'
            ]);
        }catch(\Exception $ex){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $catalog = SalesConcepts::select('name', 'price')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $catalog
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            \DB::beginTransaction();

            $concept = SalesConcepts::where('id', $id)->first();
            $concept->name = $request->name;
            $concept->price = $request->price;
            $concept->is_process = $request->is_process;
            $concept->save();

            $history = \DB::table('sales_process_history')
                            ->where('sales_process_id', $id)
                            ->where('price', $concept->price)
                            ->orderBy('id', 'desc')
                            ->first();
            
            if(!isset($history)){
                \DB::table('sales_process_history')->insert([
                    'sales_concepts_id' => $concept->id,
                    'price' => $concept->price,
                    'change_date' => date('Y-m-d')
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El elemento se actualizo correctamente'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Catalog $catalog)
    {
        //
    }
}
