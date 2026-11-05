<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Concerns\AuthorizesProcedures;
use App\Http\Controllers\Concerns\ManagesApplicants;
use App\Models\BranchOffices;
use App\Models\SalesConcepts;
use App\Models\ScheduleDetails;
use App\Models\Commissions;
use App\Models\Clients;
use App\Models\ClientsPhones;
use App\Models\ClientsPassportHistory;
use App\Models\Passport;
use App\Models\Process;
use App\Models\ProcessDocuments;
use App\Models\ProcessStatus;
use App\Models\Sales;
use App\Models\SalesBilling;
use App\Models\SalesBranchOffice;
use App\Models\SalesClients;
use App\Models\SalesCommissions;
use App\Models\SalesDetails;
use App\Models\SalesPayment;
use App\Models\SalesProcess;
use App\Models\SalesStatus;
use App\Models\SalesUsers;
use App\Models\User;

class PassportSalesController extends Controller
{
    use AuthorizesProcedures, ManagesApplicants;

    const FALLBACK_EMAIL = 'tramites@visas-premier.com';

    /** valores válidos de sales_status.status y process_status.status (enums de la BD) */
    const SALE_STATUSES    = ['Activo', 'Ficha pendiente', 'Con ficha', 'Ficha pagada', 'Finalizado'];
    const PROCESS_STATUSES = ['Inicio', 'Por pagar', 'Informado', 'Pagado', 'Entregado', 'Finalizado'];

    /**
     * Passport sales list with filters and pagination
     */
    public function list(Request $request)
    {
        if (!$request->user()->can('procedure_passport')) {
            return $this->forbidden();
        }

        $query = Sales::join('sales_clients', 'sales_clients.sales_id', 'sales.id')
                        ->join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                        ->join('sales_status', 'sales_status.sales_id', 'sales.id')
                        ->join('clients', 'clients.id', '=', 'sales_clients.clients_id')
                        ->join('process as p', function ($join) {
                            $join->on('p.clients_id', '=', 'clients.id')
                                 ->on('p.sales_id', '=', 'sales.id')
                                 ->where('p.type', 'Pasaporte');
                        })
                        ->selectRaw('
                            sales.id, sales.folio, sales.date, sales.type,
                            COUNT(DISTINCT sales_clients.id) AS no_applicants,
                            sales_billing.names, sales_billing.lastname1, sales_billing.lastname2,
                            sales_billing.email, sales_billing.phone,
                            sales_status.status
                        ')
                        ->where('sales_status.is_last', true)
                        ->groupBy(
                            'sales.id', 'sales.folio', 'sales.date', 'sales.type',
                            'sales_billing.names', 'sales_billing.lastname1', 'sales_billing.lastname2',
                            'sales_billing.email', 'sales_billing.phone', 'sales_status.status'
                        );

        if ($request->user()->role === 'ventas'){
            $query->whereExists(function($sub){
                $sub->selectRaw('1')
                    ->from('sales_users')
                    ->whereColumn('sales_users.sales_id', 'sales.id')
                    ->where('sales_users.users_id', \Auth::id());
            });
        }

        if ($request->filled('folio'))
            $query->where('sales.folio', 'like', '%' . $request->folio . '%');

        if ($request->filled('date'))
            $query->whereRaw("DATE_FORMAT(sales.date, '%d/%m/%Y') LIKE ?", ['%' . $request->date . '%']);

        if ($request->filled('email'))
            $query->where('sales_billing.email', 'like', '%' . $request->email . '%');

        if ($request->filled('status'))
            $query->where('sales_status.status', $request->status);

        if ($request->filled('client'))
            $query->whereRaw(
                "CONCAT(sales_billing.names, ' ', sales_billing.lastname1, IFNULL(CONCAT(' ', sales_billing.lastname2), '')) LIKE ?",
                ['%' . $request->client . '%']
            );

        if ($request->filled('no_applicants') && is_numeric($request->no_applicants))
            $query->havingRaw('COUNT(DISTINCT sales_clients.id) = ?', [(int) $request->no_applicants]);

        $perPage = (int) ($request->per_page ?? 25);
        $page    = (int) ($request->page ?? 1);

        $countSql = "SELECT COUNT(*) as aggregate FROM ({$query->toSql()}) as subquery";
        $total    = \DB::selectOne($countSql, $query->getBindings())->aggregate;

        $records = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $list = $records->map(function ($item) {
            return [
                'id'            => $item->id,
                'folio'         => $item->folio,
                'date'          => $item->date,
                'type'          => $item->type,
                'no_applicants' => $item->no_applicants,
                'client'        => $item->names . ' ' . $item->lastname1 . (is_null($item->lastname2) ? '' : ' ' . $item->lastname2),
                'email'         => $item->email === self::FALLBACK_EMAIL ? null : $item->email,
                'phone'         => $item->phone,
                'status'        => $item->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $list,
            'meta'    => [
                'total'        => (int) $total,
                'current_page' => $page,
                'last_page'    => max(1, (int) ceil($total / $perPage)),
                'per_page'     => $perPage,
            ]
        ]);
    }

    /**
     * Full detail of a passport sale
     */
    public function info(Request $request, $id)
    {
        if (!$this->canManageSale($request->user(), $id, 'procedure_passport')) {
            return $this->forbidden();
        }

        $sale = Sales::join('branch_office', 'branch_office.id', 'sales.branch_offices_id')
                    ->join('sales_process', 'sales_process.sales_id', 'sales.id')
                    ->join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                    ->join('sales_status', 'sales_status.sales_id', 'sales.id')
                    ->leftJoin('sales_payment', 'sales_payment.sales_id', 'sales.id')
                    ->selectRaw('
                        sales.id, branch_office.name as branch_office,
                        sales.folio, sales.date, sales.total, sales.type,
                        SUM(sales_payment.amount) as payments,
                        sales_process.advance_payment, sales_process.payable, sales_process.contact,
                        sales_billing.email, sales_billing.names, sales_billing.lastname1,
                        sales_billing.lastname2, sales_billing.phone,
                        sales_status.status
                    ')
                    ->where('sales_status.is_last', true)
                    ->where('sales.id', $id)
                    ->groupBy(
                        'sales.id', 'branch_office.name', 'sales.folio', 'sales.date',
                        'sales.total', 'sales.type', 'sales_process.advance_payment',
                        'sales_process.payable', 'sales_process.contact',
                        'sales_billing.email', 'sales_billing.names', 'sales_billing.lastname1',
                        'sales_billing.lastname2', 'sales_billing.phone', 'sales_status.status'
                    )
                    ->first();

        if (is_null($sale)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el trámite'
            ], 404);
        }

        $clients = SalesClients::join('clients', 'clients.id', 'sales_clients.clients_id')
                            ->join('process', function ($join) {
                                $join->on('process.clients_id', '=', 'clients.id')
                                     ->on('process.sales_id', '=', 'sales_clients.sales_id')
                                     ->where('process.type', 'Pasaporte');
                            })
                            ->leftJoin('passport', 'passport.process_id', 'process.id')
                            ->leftJoin('process_status', function ($join) {
                                $join->on('process_status.process_id', '=', 'process.id')
                                     ->where('process_status.is_last', true);
                            })
                            ->select(
                                'clients.id as clients_id',
                                'clients.names', 'clients.lastname1', 'clients.lastname2',
                                'clients.curp', 'clients.ine', 'clients.birthdate',
                                'clients.city as city_birth',
                                'process.id as process_id', 'process.subtype',
                                'process.age_type', 'process.option_type',
                                'process.observations', 'process.complete',
                                'passport.id as passport_id',
                                'passport.number as passport_number',
                                'passport.expedition_date',
                                'passport.expiration_date',
                                'process_status.status as process_status'
                            )
                            // un solo celular por solicitante: un join duplicaba al solicitante si tenía varios
                            ->selectSub(
                                ClientsPhones::select('number')
                                    ->whereColumn('clients_phones.clients_id', 'clients.id')
                                    ->where('type', 'Celular')
                                    ->orderBy('id')
                                    ->limit(1),
                                'phone'
                            )
                            ->where('sales_clients.sales_id', $id)
                            ->get();

        foreach ($clients as $client) {
            $client['schedule'] = \DB::table('schedule_details')
                                        ->where('clients_id', $client->clients_id)
                                        ->where('sales_id', $id)
                                        ->select('office', 'city', 'appointment_date', 'schedule', 'status', 'observations')
                                        ->get();
        }

        if ($sale->email === self::FALLBACK_EMAIL) {
            $sale->email = null;
        }

        $sale['clients'] = $clients;

        return response()->json([
            'success' => true,
            'data'    => $sale
        ]);
    }

    /**
     * Create a new passport sale (individual, group or family)
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            $saleData = $request->sales;

            $sales = new Sales();
            $sales->branch_offices_id = $saleData['branch_office_id'];
            $sales->date              = $saleData['date'];
            $sales->folio             = $saleData['folio'];
            $sales->total             = $saleData['total'];
            $sales->type              = $saleData['type'];
            $sales->save();

            /** commissions */
            foreach ($request->commissions as $commissionItem) {
                if ($commissionItem['type'] == 'seller') {
                    $salesUser           = new SalesUsers();
                    $salesUser->sales_id = $sales->id;
                    $salesUser->users_id = $commissionItem['users_id'];
                    $salesUser->save();
                }

                $commission = Commissions::join('users_commissions', 'users_commissions.commissions_id', 'commissions.id')
                                ->select('commissions.concept', 'users_commissions.amount')
                                ->where('commissions.concept', $commissionItem['commission'])
                                ->where('users_commissions.users_id', $commissionItem['users_id'])
                                ->first();

                if ($commission) {
                    $saleCommission           = new SalesCommissions();
                    $saleCommission->sales_id = $sales->id;
                    $saleCommission->users_id = $commissionItem['users_id'];
                    $saleCommission->concept  = $commission->concept;
                    $saleCommission->amount   = $commission->amount;
                    $saleCommission->save();
                }
            }

            /** sale details (concepts) */
            foreach ($request->details as $detailItem) {
                $conceptId = $detailItem['sales_concepts_id'] ?? null;

                if (empty($conceptId)) {
                    $concept = SalesConcepts::where('name', 'LIKE', '%Pasaporte%')
                                    ->where('is_process', true)
                                    ->first();

                    if (!$concept) {
                        $concept             = new SalesConcepts();
                        $concept->name       = 'Pasaporte';
                        $concept->price      = 0;
                        $concept->is_process = true;
                        $concept->save();

                        \DB::table('sales_concepts_history')->insert([
                            'sales_concepts_id' => $concept->id,
                            'price'             => 0,
                            'change_date'       => date('Y-m-d')
                        ]);
                    }

                    $conceptId = $concept->id;
                }

                $salesDetail                    = new SalesDetails();
                $salesDetail->sales_id          = $sales->id;
                $salesDetail->sales_concepts_id = $conceptId;
                $salesDetail->amount            = $detailItem['amount'];
                $salesDetail->save();
            }

            /** branch office snapshot */
            $branchOffice = BranchOffices::where('id', $sales->branch_offices_id)->first();
            SalesBranchOffice::create(['sales_id' => $sales->id, 'name' => $branchOffice->name]);

            /** initial payment */
            $payment = $request->payment;
            SalesPayment::create([
                'sales_id'       => $sales->id,
                'method_payment' => $payment['method_payment'],
                'amount'         => $payment['amount'],
                'status'         => 'Pagado',
                'platform'       => $payment['platform'],
                'receipt'        => $payment['receipt'],
                'reference'      => $payment['reference'] ?? null
            ]);

            /** billing contact */
            $billing = $request->billing;
            $billing['email'] = trim($billing['email'] ?? '') !== '' ? $billing['email'] : self::FALLBACK_EMAIL;

            SalesBilling::create([
                'sales_id'  => $sales->id,
                'email'     => $billing['email'],
                'names'     => $billing['names'],
                'lastname1' => $billing['lastname1'],
                'lastname2' => $billing['lastname2'],
                'phone'     => $billing['phone']
            ]);

            /** client user account */
            $user = User::where('email', $billing['email'])->where('role', 'cliente')->first();
            $is_new_user = is_null($user);

            if ($is_new_user) {
                $user            = new User();
                $user->names     = $billing['names'];
                $user->lastname1 = $billing['lastname1'];
                $user->lastname2 = $billing['lastname2'];
                $user->email     = strtolower($billing['names'] . '.' . $billing['lastname1'] . '.' . \Str::random(4) . '@visas-premier.com');
                $user->password  = bcrypt('@cc350.T3mp.2025');
                $user->role      = 'cliente';
                $user->save();
            }

            $salesUser           = new SalesUsers();
            $salesUser->sales_id = $sales->id;
            $salesUser->users_id = $user->id;
            $salesUser->save();

            /** sale status */
            SalesStatus::create(['sales_id' => $sales->id, 'status' => 'Activo', 'is_last' => true]);

            /** sale process (payment info) */
            $processData                        = $request->sale_process;
            $salesProcess                       = new SalesProcess();
            $salesProcess->sales_id             = $sales->id;
            $salesProcess->status               = 'Incompleto';
            $salesProcess->advance_payment      = $processData['advance_payment'];
            $salesProcess->payable              = $processData['payable'];
            $salesProcess->contact              = $processData['contact'];
            $salesProcess->save();

            /** applicants */
            foreach ($request->clients as $item) {
                if (isset($item['client']['id'])) {
                    $client = Clients::where('id', $item['client']['id'])->first();
                } else {
                    $client = new Clients();
                }

                $client->names            = $item['client']['names'];
                $client->lastname1        = $item['client']['lastname1'];
                $client->lastname2        = $item['client']['lastname2'];
                $client->curp             = $item['client']['curp'];
                $client->birthdate        = $item['client']['birthdate'];
                $client->city             = $item['client']['city'];
                $client->country_birth_id = $item['client']['country_birth_id'];
                $client->state_birth_id   = $item['client']['state_birth_id'];
                $client->save();

                foreach ($item['phones'] as $phone) {
                    if (isset($phone['id'])) {
                        $clientPhone = ClientsPhones::where('id', $phone['id'])->first();
                    } else {
                        $clientPhone = new ClientsPhones();
                    }
                    $clientPhone->clients_id = $client->id;
                    $clientPhone->type       = $phone['type'];
                    $clientPhone->number     = $phone['number'];
                    $clientPhone->save();
                }

                SalesClients::create(['sales_id' => $sales->id, 'clients_id' => $client->id]);

                $process               = new Process();
                $process->sales_id     = $sales->id;
                $process->clients_id   = $client->id;
                $process->type         = 'Pasaporte';
                $process->subtype      = $item['process']['subtype'];
                $process->age_type     = $item['process']['age_type'];
                $process->option_type  = $item['process']['option_type'] ?? null;
                $process->observations = $item['process']['observations'] ?? null;
                $process->save();

                ProcessStatus::create(['process_id' => $process->id, 'status' => 'Inicio', 'is_last' => true]);

                /** previous passport data (required for renewals, optional for first time) */
                if (isset($item['passport'])) {
                    $passport                         = new Passport();
                    $passport->process_id             = $process->id;
                    $passport->number                 = $item['passport']['number'];
                    $passport->expedition_date        = $item['passport']['expedition_date'];
                    $passport->expiration_date        = $item['passport']['expiration_date'];
                    $passport->expedition_countries_id = $item['passport']['expedition_country_id'] ?? null;
                    $passport->save();

                    $findPassport = ClientsPassportHistory::where('clients_id', $client->id)
                                        ->where('number', $passport->number)
                                        ->first();
                    if (!$findPassport) {
                        ClientsPassportHistory::create([
                            'clients_id'      => $client->id,
                            'number'          => $passport->number,
                            'expedition_date' => $passport->expedition_date,
                            'expiration_date' => $passport->expiration_date
                        ]);
                    }
                }
            }

            $this->sendWelcomeEmail($sales->id, $is_new_user);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro de pasaporte se realizó correctamente'
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ]);
        }
    }

    /**
     * Edición de una venta de pasaporte: datos de venta, abono y estatus (ficha) y,
     * con el formulario de alta, solicitantes (existentes y nuevos), precio y abono
     */
    public function update($id, Request $request)
    {
        if ($this->isClientUser($request->user()) || !$this->canManageSale($request->user(), $id, 'procedure_passport_update')) {
            return $this->forbidden();
        }

        $sale = Sales::find($id);

        if (is_null($sale)) {
            return response()->json(['success' => false, 'message' => 'No se encontró el trámite'], 404);
        }

        if ($error = $this->invalidSaleEdit($request->all(), false)) {
            return response()->json(['success' => false, 'message' => $error], 422);
        }

        /** abono: numérico, entre 0 y el total (el nuevo, si se recalcula); el saldo siempre es total - abono */
        if ($request->filled('sale_process')) {
            $advance = $request->input('sale_process.advance_payment');
            $total   = $request->filled('pricing') ? round((float) $request->input('pricing.total'), 2) : (float) $sale->total;

            if (!is_numeric($advance) || $advance < 0 || $advance > $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'El abono debe ser un monto entre 0 y el total de la venta ($' . $total . ')'
                ], 422);
            }
        }

        if ($request->filled('status') && !in_array($request->status, self::SALE_STATUSES, true)) {
            return response()->json(['success' => false, 'message' => 'Estatus de venta no válido'], 422);
        }

        foreach ($request->input('clients', []) as $item) {
            if (isset($item['process_status']) && !in_array($item['process_status'], self::PROCESS_STATUSES, true)) {
                return response()->json(['success' => false, 'message' => 'Estatus de trámite no válido'], 422);
            }
        }

        try {
            \DB::beginTransaction();

            $this->updateSaleGeneral($sale, [
                'type'    => $request->input('type'),
                'billing' => $request->input('billing'),
                'contact' => $request->input('contact', $request->input('sale_process.contact')),
            ], self::FALLBACK_EMAIL);

            /** solicitantes nuevos */
            foreach ($request->input('new_clients', []) as $item) {
                $this->addApplicantToSale($sale, $item, 'Pasaporte');
            }

            /** precio recalculado y abono adicional (formulario de edición) */
            $this->applyPricingAndPayment($sale, 'Pasaporte', $request->input('pricing'), $request->input('payment'));

            /** abono capturado directamente (modal de datos de venta) */
            if ($request->filled('sale_process')) {
                $salesProcess = SalesProcess::where('sales_id', $id)->first();

                if ($salesProcess) {
                    $advance = (float) $request->input('sale_process.advance_payment');

                    $salesProcess->advance_payment = round($advance, 2);
                    $salesProcess->payable         = round((float) $sale->total - $advance, 2);
                    $salesProcess->save();
                }
            }

            /** sale status: only a new history row when it actually changes */
            if ($request->filled('status')) {
                $currentStatus = SalesStatus::where('sales_id', $id)->orderByDesc('id')->value('status');

                if ($currentStatus !== $request->status) {
                    SalesStatus::where('sales_id', $id)->update(['is_last' => false]);
                    SalesStatus::create(['sales_id' => $id, 'status' => $request->status, 'is_last' => true]);
                }
            }

            /** solicitantes existentes */
            foreach ($request->input('clients', []) as $item) {
                /** only processes that belong to this sale can be edited */
                $process = Process::where('id', $item['process_id'] ?? null)
                                ->where('sales_id', $id)
                                ->first();

                if (!$process) {
                    continue;
                }

                // solo se tocan los campos enviados
                if (array_key_exists('observations', $item)) {
                    $process->observations = $item['observations'];
                }
                if (array_key_exists('complete', $item)) {
                    $process->complete = (bool) $item['complete'];
                }
                $process->save();

                /** datos del solicitante, tipo de trámite y pasaporte anterior */
                $this->updateApplicant($sale, $item, 'Pasaporte');

                /** per-client process status: only a new history row when it actually changes */
                $currentProcessStatus = ProcessStatus::where('process_id', $process->id)->orderByDesc('id')->value('status');

                if (isset($item['process_status']) && $currentProcessStatus !== $item['process_status']) {
                    ProcessStatus::where('process_id', $process->id)->update(['is_last' => false]);
                    ProcessStatus::create([
                        'process_id' => $process->id,
                        'status'     => $item['process_status'],
                        'is_last'    => true
                    ]);
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro se actualizó correctamente'
            ]);

        } catch (\RuntimeException $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ]);
        }
    }

    /**
     * Delete a passport sale and its cascaded records
     */
    public function delete(Request $request, $id)
    {
        if ($this->isClientUser($request->user()) || !$this->canManageSale($request->user(), $id, 'procedure_passport_delete')) {
            return $this->forbidden();
        }

        try {
            \DB::beginTransaction();

            $sale = Sales::findOrFail($id);
            $sale->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro se eliminó correctamente'
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ]);
        }
    }

    /**
     * Update or create schedule appointment for passport clients
     */
    public function scheduleUpdate($id, Request $request)
    {
        if (!$this->canManageSale($request->user(), $id, 'procedure_passport_update')) {
            return $this->forbidden();
        }

        $saleClientsIds = SalesClients::where('sales_id', $id)->pluck('clients_id')->all();

        try {
            \DB::beginTransaction();

            foreach ($request->all() as $item) {
                if (!in_array($item['clients_id'], $saleClientsIds)) {
                    continue;
                }

                $schedule = ScheduleDetails::where('sales_id', $id)
                                ->where('clients_id', $item['clients_id'])
                                ->where('office', $item['office'])
                                ->first();

                if (is_null($schedule)) {
                    $schedule             = new ScheduleDetails();
                    $schedule->sales_id   = $id;
                    $schedule->clients_id = $item['clients_id'];
                    $schedule->office     = $item['office'];
                }

                $schedule->appointment_date = $item['appointment_date'];
                $schedule->schedule         = $item['schedule']        ?? null;
                $schedule->city             = $item['city']            ?? null;
                $schedule->status           = ($item['status'] ?? null) ?: 'Activo';
                $schedule->observations     = $item['observations']    ?? null;
                $schedule->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Los datos se guardaron correctamente'
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ]);
        }
    }

    private function sendWelcomeEmail($sales_id, $is_new_user = true)
    {
        try {
            $sale = Sales::join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                        ->select('sales.date', 'sales.folio', 'sales_billing.email', 'sales_billing.names', 'sales_billing.lastname1', 'sales_billing.lastname2')
                        ->where('sales.id', $sales_id)
                        ->first();

            $sale->date = date_format(date_create($sale->date), 'd/m/Y');

            $salesClients = SalesClients::join('clients', 'clients.id', 'sales_clients.clients_id')
                                    ->join('process', function ($join) {
                                        $join->on('process.clients_id', '=', 'clients.id')
                                             ->on('process.sales_id', '=', 'sales_clients.sales_id')
                                             ->where('process.type', 'Pasaporte');
                                    })
                                    ->select(
                                        'clients.names', 'clients.lastname1', 'clients.lastname2',
                                        'process.type', 'process.subtype', 'process.age_type', 'process.option_type', 'process.visa_type'
                                    )
                                    ->where('sales_clients.sales_id', $sales_id)
                                    ->get();

            $documents = ProcessDocuments::select('documents')->where('type', 'Pasaporte')->first();

            $user = SalesUsers::join('users', 'users.id', 'sales_users.users_id')
                            ->where('sales_id', $sales_id)
                            ->where('users.role', 'cliente')
                            ->select('email', 'names', 'lastname1', 'lastname2')
                            ->first();

            $data = [
                'body' => [
                    'sale'         => $sale,
                    'clients'      => $salesClients,
                    'documents'    => $documents,
                    'user'         => $user,
                    'is_new_user'  => $is_new_user,
                ],
                'sender'   => 'postmaster@visas-premier.com',
                'subject'  => 'Pasaporte - ' . $sale->folio,
                'receiver' => $sale->email
            ];

            \Mail::send('emails.sales.register', ['data' => $data], function ($m) use ($data) {
                $m->from($data['sender'], 'Visas premier');
                $receiverName = $data['body']['sale']['names'] . ' ' . $data['body']['sale']['lastname1']
                    . (is_null($data['body']['sale']['lastname2']) ? '' : ' ' . $data['body']['sale']['lastname2']);
                $m->to($data['receiver'], $receiverName)->subject($data['subject']);
            });

        } catch (\Exception $e) {
            \Log::error('Passport welcome email failed: ' . $e->getMessage());
        }
    }
}
