<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    const FALLBACK_EMAIL = 'tramites@visas-premier.com';

    /**
     * Passport sales list with filters and pagination
     */
    public function list(Request $request)
    {
        $query = Sales::join('sales_clients', 'sales_clients.sales_id', 'sales.id')
                        ->join('sales_billing', 'sales_billing.sales_id', 'sales.id')
                        ->join('sales_status', 'sales_status.sales_id', 'sales.id')
                        ->join('clients', 'clients.id', '=', 'sales_clients.clients_id')
                        ->join('process as p', function ($join) {
                            $join->on('p.clients_id', '=', 'clients.id')
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
    public function info($id)
    {
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

        $clients = SalesClients::join('clients', 'clients.id', 'sales_clients.clients_id')
                            ->join('process', function ($join) {
                                $join->on('process.clients_id', '=', 'clients.id')
                                     ->where('process.type', 'Pasaporte');
                            })
                            ->leftJoin('passport', 'passport.process_id', 'process.id')
                            ->leftJoin('process_status', function ($join) {
                                $join->on('process_status.process_id', '=', 'process.id')
                                     ->where('process_status.is_last', true);
                            })
                            ->leftJoin('clients_phones', function ($join) {
                                $join->on('clients_phones.clients_id', '=', 'clients.id')
                                     ->where('clients_phones.type', 'Celular');
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
                                'process_status.status as process_status',
                                'clients_phones.number as phone'
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
     * Update billing, payment summary and per-client process data
     */
    public function update($id, Request $request)
    {
        try {
            \DB::beginTransaction();

            /** billing contact */
            if ($request->filled('billing')) {
                $billing = $request->billing;
                $salesBilling = SalesBilling::where('sales_id', $id)->first();
                if ($salesBilling) {
                    $salesBilling->email     = trim($billing['email'] ?? '') !== '' ? $billing['email'] : self::FALLBACK_EMAIL;
                    $salesBilling->names     = $billing['names'];
                    $salesBilling->lastname1 = $billing['lastname1'];
                    $salesBilling->lastname2 = $billing['lastname2'];
                    $salesBilling->phone     = $billing['phone'];
                    $salesBilling->save();
                }
            }

            /** payment summary (advance / payable) */
            if ($request->filled('sale_process')) {
                $processData = $request->sale_process;
                $salesProcess = SalesProcess::where('sales_id', $id)->first();
                if ($salesProcess) {
                    $salesProcess->advance_payment = $processData['advance_payment'];
                    $salesProcess->payable         = $processData['payable'];
                    $salesProcess->contact         = $processData['contact'];
                    $salesProcess->save();
                }
            }

            /** sale status */
            if ($request->filled('status')) {
                SalesStatus::where('sales_id', $id)->update(['is_last' => false]);
                SalesStatus::create(['sales_id' => $id, 'status' => $request->status, 'is_last' => true]);
            }

            /** per-client process observations */
            if ($request->filled('clients')) {
                foreach ($request->clients as $item) {
                    if (isset($item['process_id'])) {
                        $process = Process::where('id', $item['process_id'])->first();
                        if ($process) {
                            $process->observations = $item['observations'] ?? null;
                            $process->complete     = $item['complete'] ?? $process->complete;
                            $process->save();
                        }
                    }

                    /** update per-client process status */
                    if (isset($item['process_id']) && isset($item['process_status'])) {
                        ProcessStatus::where('process_id', $item['process_id'])->update(['is_last' => false]);
                        ProcessStatus::create([
                            'process_id' => $item['process_id'],
                            'status'     => $item['process_status'],
                            'is_last'    => true
                        ]);
                    }
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'El registro se actualizó correctamente'
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
     * Delete a passport sale and its cascaded records
     */
    public function delete($id)
    {
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
        try {
            \DB::beginTransaction();

            foreach ($request->all() as $item) {
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
                $schedule->status           = $item['status']          ?? null;
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
                                             ->where('process.type', 'Pasaporte');
                                    })
                                    ->select('clients.names', 'clients.lastname1', 'clients.lastname2', 'process.subtype', 'process.age_type')
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
