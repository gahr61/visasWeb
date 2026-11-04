<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Liga cada trámite (process) con la venta que lo originó.
 *
 * Antes `process` solo tenía `clients_id`, por lo que un cliente con varias
 * ventas (p. ej. una visa y después un pasaporte) mezclaba sus trámites entre
 * ventas al consultar o editar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('process', function (Blueprint $table) {
            $table->bigInteger('sales_id')->unsigned()->nullable()->after('id')->index();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });

        $this->backfill();
    }

    /**
     * Venta y trámite se crean en la misma transacción (visa_store / store),
     * así que la venta de un trámite es la más reciente del mismo cliente
     * creada en o antes del `created_at` del trámite.
     */
    private function backfill(): void
    {
        DB::table('process')
            ->whereNull('sales_id')
            ->select('id', 'clients_id', 'created_at')
            // chunkById: chunk() con offset saltaría filas al ir llenando sales_id
            ->chunkById(500, function ($processes) {
                foreach ($processes as $process) {
                    $base = DB::table('sales_clients')
                        ->join('sales', 'sales.id', '=', 'sales_clients.sales_id')
                        ->where('sales_clients.clients_id', $process->clients_id);

                    $salesId = null;

                    if (!is_null($process->created_at)) {
                        $salesId = (clone $base)
                            ->where('sales.created_at', '<=', $process->created_at)
                            ->orderByDesc('sales.id')
                            ->value('sales.id');
                    }

                    // Sin fecha comparable: la primera venta del cliente
                    if (is_null($salesId)) {
                        $salesId = (clone $base)->orderBy('sales.id')->value('sales.id');
                    }

                    if (!is_null($salesId)) {
                        DB::table('process')->where('id', $process->id)->update(['sales_id' => $salesId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('process', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->dropColumn('sales_id');
        });
    }
};
