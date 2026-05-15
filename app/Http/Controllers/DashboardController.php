<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function indicators(Request $request)
    {
        try {
            $user       = \Auth::user();
            $isVendedor = $user->role === 'vendedor';

            $now       = Carbon::now();
            $startDate = $request->input('start_date', $now->copy()->startOfMonth()->toDateString());
            $endDate   = $request->input('end_date',   $now->copy()->endOfMonth()->toDateString());
            $today     = $now->toDateString();
            $next7Days = $now->copy()->addDays(7)->toDateString();

            $data = [];

            // ── Mis ventas en el período ─────────────────────────────
            $mySalesIds = \DB::table('sales')
                ->join('sales_users', 'sales_users.sales_id', '=', 'sales.id')
                ->where('sales_users.users_id', $user->id)
                ->whereBetween('sales.date', [$startDate, $endDate])
                ->pluck('sales.id')
                ->unique()
                ->values()
                ->toArray();

            $data['my_sales'] = [
                'count'  => count($mySalesIds),
                'amount' => (float) (empty($mySalesIds)
                    ? 0
                    : \DB::table('sales')->whereIn('id', $mySalesIds)->sum('total')),
            ];

            // ── Mis clientes (histórico, sin filtro de fecha) ────────
            $myClientIds = \DB::table('sales_clients')
                ->join('sales_users', 'sales_users.sales_id', '=', 'sales_clients.sales_id')
                ->where('sales_users.users_id', $user->id)
                ->pluck('sales_clients.clients_id')
                ->unique()
                ->values()
                ->toArray();

            // ── Mis trámites activos y por estado (estado actual) ────
            $myProcessIds = empty($myClientIds)
                ? []
                : \DB::table('process')
                    ->whereIn('clients_id', $myClientIds)
                    ->pluck('id')
                    ->toArray();

            $data['my_procedures'] = [
                'active'    => empty($myProcessIds) ? 0 : \DB::table('process_status')
                    ->whereIn('process_id', $myProcessIds)
                    ->where('is_last', true)
                    ->where('status', '!=', 'Finalizado')
                    ->count(),
                'by_status' => empty($myProcessIds) ? [] : \DB::table('process_status')
                    ->whereIn('process_id', $myProcessIds)
                    ->where('is_last', true)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get(),
            ];

            // ── Mis citas próximas (siempre los próximos 7 días) ─────
            $data['my_upcoming_appointments'] = empty($myClientIds) ? 0
                : \DB::table('schedule_details')
                    ->whereIn('clients_id', $myClientIds)
                    ->where('status', 'Activo')
                    ->whereBetween('appointment_date', [$today, $next7Days])
                    ->count();

            // ── Mis clientes nuevos en el período ────────────────────
            $data['my_new_clients'] = \DB::table('sales_clients')
                ->join('sales', 'sales.id', '=', 'sales_clients.sales_id')
                ->join('sales_users', 'sales_users.sales_id', '=', 'sales_clients.sales_id')
                ->where('sales_users.users_id', $user->id)
                ->whereBetween('sales.date', [$startDate, $endDate])
                ->distinct('sales_clients.clients_id')
                ->count('sales_clients.clients_id');

            // ── Mis comisiones en el período ─────────────────────────
            $data['my_commissions'] = [
                'count'  => \DB::table('sales_commissions')
                    ->join('sales', 'sales.id', '=', 'sales_commissions.sales_id')
                    ->where('sales_commissions.users_id', $user->id)
                    ->whereBetween('sales.date', [$startDate, $endDate])
                    ->count(),
                'amount' => (float) \DB::table('sales_commissions')
                    ->join('sales', 'sales.id', '=', 'sales_commissions.sales_id')
                    ->where('sales_commissions.users_id', $user->id)
                    ->whereBetween('sales.date', [$startDate, $endDate])
                    ->sum('sales_commissions.amount'),
            ];

            // ── Mis ventas por sucursal ───────────────────────────────
            $data['my_sales_by_branch'] = empty($mySalesIds) ? [] : \DB::table('sales')
                ->join('branch_office', 'branch_office.id', '=', 'sales.branch_offices_id')
                ->whereIn('sales.id', $mySalesIds)
                ->selectRaw('branch_office.name, COUNT(sales.id) as count, SUM(sales.total) as amount')
                ->groupBy('branch_office.id', 'branch_office.name')
                ->orderByDesc('count')
                ->get();

            // ── Indicadores solo para admin / gerencia ───────────────
            if (!$isVendedor) {
                // Ventas globales en el período
                $data['total_sales'] = [
                    'count'  => \DB::table('sales')
                        ->whereBetween('date', [$startDate, $endDate])
                        ->count(),
                    'amount' => (float) \DB::table('sales')
                        ->whereBetween('date', [$startDate, $endDate])
                        ->sum('total'),
                ];

                // Ventas pendientes de pago (estado actual)
                $data['pending_payment'] = \DB::table('sales')
                    ->join('sales_status', 'sales_status.sales_id', '=', 'sales.id')
                    ->where('sales_status.is_last', true)
                    ->whereIn('sales_status.status', ['Ficha pendiente', 'Activo'])
                    ->distinct('sales.id')
                    ->count('sales.id');

                // Todos los trámites por estado (estado actual)
                $data['all_procedures_by_status'] = \DB::table('process_status')
                    ->where('is_last', true)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get();

                $data['total_active_procedures'] = \DB::table('process_status')
                    ->where('is_last', true)
                    ->where('status', '!=', 'Finalizado')
                    ->count();

                // Clientes
                $data['total_clients'] = \DB::table('clients')->count();
                $data['new_clients']   = \DB::table('sales_clients')
                    ->join('sales', 'sales.id', '=', 'sales_clients.sales_id')
                    ->whereBetween('sales.date', [$startDate, $endDate])
                    ->distinct('sales_clients.clients_id')
                    ->count('sales_clients.clients_id');

                // Comisiones globales en el período
                $data['total_commissions'] = (float) \DB::table('sales_commissions')
                    ->join('sales', 'sales.id', '=', 'sales_commissions.sales_id')
                    ->whereBetween('sales.date', [$startDate, $endDate])
                    ->sum('sales_commissions.amount');

                $data['total_commissions_count'] = \DB::table('sales_commissions')
                    ->join('sales', 'sales.id', '=', 'sales_commissions.sales_id')
                    ->whereBetween('sales.date', [$startDate, $endDate])
                    ->count();

                // Ventas por sucursal en el período
                $data['sales_by_branch'] = \DB::table('sales')
                    ->join('branch_office', 'branch_office.id', '=', 'sales.branch_offices_id')
                    ->whereBetween('sales.date', [$startDate, $endDate])
                    ->selectRaw('branch_office.name, COUNT(sales.id) as count, SUM(sales.total) as amount')
                    ->groupBy('branch_office.id', 'branch_office.name')
                    ->orderByDesc('count')
                    ->get();

                // Ventas por tipo en el período
                $data['sales_by_type'] = \DB::table('sales')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->selectRaw('type, COUNT(*) as count, SUM(total) as amount')
                    ->groupBy('type')
                    ->orderByDesc('count')
                    ->get();

                // Ranking de empleados en el período (top 5)
                $data['employees_ranking'] = \DB::table('users')
                    ->join('sales_users', 'sales_users.users_id', '=', 'users.id')
                    ->join('sales', 'sales.id', '=', 'sales_users.sales_id')
                    ->where('users.role', '!=', 'cliente')
                    ->whereBetween('sales.date', [$startDate, $endDate])
                    ->selectRaw("users.id, CONCAT(users.names, ' ', users.lastname1) as name, COUNT(DISTINCT sales.id) as sales_count")
                    ->groupBy('users.id', 'users.names', 'users.lastname1')
                    ->orderByDesc('sales_count')
                    ->limit(5)
                    ->get();

                // Empleados activos (sin filtro de fecha)
                $data['active_employees'] = \DB::table('users')
                    ->where('role', '!=', 'cliente')
                    ->count();

                // Solicitudes de contacto web en el período
                $data['web_contacts'] = \DB::table('web_contact')
                    ->whereBetween(\DB::raw('DATE(created_at)'), [$startDate, $endDate])
                    ->count();

                // Tokens de confirmación pendientes (no expirados)
                $data['pending_tokens'] = \DB::table('sales_confirm_token')
                    ->where('expires_at', '>', $now)
                    ->count();

                // Citas próximas globales (próximos 7 días)
                $data['all_upcoming_appointments'] = \DB::table('schedule_details')
                    ->where('status', 'Activo')
                    ->whereBetween('appointment_date', [$today, $next7Days])
                    ->count();
            }

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => basename($e->getFile()),
            ], 500);
        }
    }
}
