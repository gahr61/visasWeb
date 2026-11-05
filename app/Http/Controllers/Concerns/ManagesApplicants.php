<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Clients;
use App\Models\ClientsPassportHistory;
use App\Models\ClientsPhones;
use App\Models\ClientsVisaHistory;
use App\Models\Passport;
use App\Models\Process;
use App\Models\ProcessStatus;
use App\Models\Sales;
use App\Models\SalesBilling;
use App\Models\SalesClients;
use App\Models\SalesDetails;
use App\Models\SalesPayment;
use App\Models\SalesProcess;
use App\Models\Visas;

/**
 * Edición de una venta con el mismo formulario del alta (visa y pasaporte):
 * datos de solicitantes, alta de solicitantes, precio y abono.
 * Requiere AuthorizesProcedures en la misma clase.
 */
trait ManagesApplicants
{
    protected static array $SALE_TYPES      = ['Individual', 'Familiar', 'Grupo'];
    protected static array $PAYMENT_METHODS = ['Efectivo', 'Tarjeta', 'Depósito Bancario', 'Transferencia Electroníca'];

    /* ───────────────────────────── lectura ───────────────────────────── */

    /** datos de la venta en el formato del formulario de alta */
    protected function saleEditData(Sales $sale, string $type): array
    {
        $billing      = SalesBilling::where('sales_id', $sale->id)->first();
        $salesProcess = SalesProcess::where('sales_id', $sale->id)->first();

        $processes = Process::where('sales_id', $sale->id)
                        ->where('type', $type)
                        ->orderBy('id')
                        ->get();

        $clients = $processes->map(function (Process $process) {
            $client   = Clients::find($process->clients_id);
            $phone    = ClientsPhones::where('clients_id', $process->clients_id)->where('type', 'Celular')->orderBy('id')->first();
            $passport = Passport::where('process_id', $process->id)->orderByDesc('id')->first();
            $visa     = Visas::where('process_id', $process->id)->orderByDesc('id')->first();

            return [
                'process_id'  => $process->id,
                'subtype'     => $process->subtype,
                'age_type'    => $process->age_type,
                'option_type' => $process->option_type,
                'visa_type'   => $process->visa_type,
                'client'      => [
                    'id'        => $client?->id,
                    'names'     => $client?->names,
                    'lastname1' => $client?->lastname1,
                    'lastname2' => $client?->lastname2,
                    'curp'      => $client?->curp,
                    'birthdate' => $client?->birthdate,
                    'country'   => $client?->country_birth_id,
                    'state'     => $client?->state_birth_id,
                    'city'      => $client?->city,
                    'phone'     => $phone?->number,
                ],
                'passport' => $passport ? [
                    'number'             => $passport->number,
                    'expedition_date'    => $passport->expedition_date,
                    'expiration_date'    => $passport->expiration_date,
                    'country_expedition' => $passport->expedition_countries_id,
                    'state_expedition'   => $passport->expedition_states_id,
                    'city_expedition'    => $passport->expedition_city,
                ] : null,
                'visa' => $visa ? [
                    'number'          => $visa->number,
                    'expedition_date' => $visa->expedition_date,
                    'expiration_date' => $visa->expiration_date,
                    'expedition_city' => $visa->expedition_city,
                ] : null,
            ];
        });

        $detail = SalesDetails::where('sales_id', $sale->id)->orderBy('id')->first();

        return [
            'id'              => $sale->id,
            'folio'           => $sale->folio,
            'type'            => $sale->type,
            'total'           => (float) $sale->total,
            'paid'            => $this->salePaid($sale, $type),
            'concept_id'      => $detail?->sales_concepts_id,
            'contact'         => $salesProcess?->contact,
            'billing'         => $billing ? [
                'names'     => $billing->names,
                'lastname1' => $billing->lastname1,
                'lastname2' => $billing->lastname2,
                'email'     => $billing->email,
                'phone'     => $billing->phone,
            ] : null,
            'clients'         => $clients,
        ];
    }

    /** pagado a la fecha: visa = suma de pagos; pasaporte = abono registrado */
    protected function salePaid(Sales $sale, string $type): float
    {
        if ($type === 'Visa') {
            return (float) SalesPayment::where('sales_id', $sale->id)->where('status', 'Pagado')->sum('amount');
        }

        return (float) (SalesProcess::where('sales_id', $sale->id)->value('advance_payment') ?? 0);
    }

    /* ──────────────────────────── validación ─────────────────────────── */

    /** valida los datos personales básicos de un solicitante */
    protected function invalidApplicantData(?array $client): ?string
    {
        if (is_null($client)) {
            return null;
        }

        if (trim($client['names'] ?? '') === '' || trim($client['lastname1'] ?? '') === '' || trim($client['curp'] ?? '') === '') {
            return 'Nombre, apellido paterno y CURP del solicitante son obligatorios';
        }

        if (!empty($client['birthdate']) && strtotime($client['birthdate']) === false) {
            return 'La fecha de nacimiento no es válida';
        }

        return null;
    }

    /** valida un solicitante (existente o nuevo) del formulario */
    protected function invalidApplicant(array $item, bool $isVisa, bool $isNew): ?string
    {
        if ($error = $this->invalidApplicantData($isNew ? ($item['client'] ?? []) : ($item['client'] ?? null))) {
            return $error;
        }

        $process = $item['process'] ?? null;

        if ($isNew || !is_null($process)) {
            $process = $process ?? [];

            if ($isVisa && $isNew && !array_key_exists('visa_type', $process)) {
                return 'El tipo de visa es obligatorio';
            }

            $required = $isNew ? ['subtype' => null, 'age_type' => null] : [];

            if ($error = $this->invalidProcessType(array_merge($required, $process), $isVisa)) {
                return $error;
            }
        }

        $passport  = $item['passport'] ?? null;
        $hasNumber = trim($passport['number'] ?? '') !== '';

        if ($isVisa && $isNew && !$hasNumber) {
            return 'El número de pasaporte del solicitante es obligatorio';
        }

        if ($hasNumber && empty($passport['expiration_date'])) {
            return 'La fecha de vencimiento del pasaporte es obligatoria';
        }

        $visa = $item['visa'] ?? null;

        if (trim($visa['number'] ?? '') !== '' && empty($visa['expiration_date'])) {
            return 'La fecha de vencimiento de la visa anterior es obligatoria';
        }

        return null;
    }

    /** valida los datos generales de la edición: tipo, precio y abono */
    protected function invalidSaleEdit(array $data, bool $isVisa): ?string
    {
        if (!empty($data['type']) && !in_array($data['type'], self::$SALE_TYPES, true)) {
            return 'Tipo de trámite no válido';
        }

        if (!empty($data['billing'])) {
            $billing = $data['billing'];

            if (trim($billing['names'] ?? '') === '' || trim($billing['lastname1'] ?? '') === '' || trim($billing['phone'] ?? '') === '') {
                return 'Nombre, apellido paterno y teléfono de quien realiza el trámite son obligatorios';
            }
        }

        foreach ($data['clients'] ?? [] as $item) {
            if ($error = $this->invalidApplicant($item, $isVisa, false)) {
                return $error;
            }
        }

        foreach ($data['new_clients'] ?? [] as $item) {
            if ($error = $this->invalidApplicant($item, $isVisa, true)) {
                return $error;
            }
        }

        if (isset($data['pricing'])) {
            $total = $data['pricing']['total'] ?? null;

            if (!is_numeric($total) || $total < 0) {
                return 'El total del trámite no es válido';
            }
        }

        if (isset($data['payment'])) {
            $payment = $data['payment'];

            if (!is_numeric($payment['amount'] ?? null) || $payment['amount'] <= 0) {
                return 'El monto del abono no es válido';
            }

            if (!in_array($payment['method_payment'] ?? null, self::$PAYMENT_METHODS, true)) {
                return 'Seleccione el método de pago del abono';
            }

            if (trim((string) ($payment['receipt'] ?? '')) === '') {
                return 'El número de recibo del abono es obligatorio';
            }
        }

        return null;
    }

    /* ──────────────────────────── escritura ──────────────────────────── */

    /** actualiza los datos personales (y celular) de un cliente; solo los campos enviados */
    protected function updateApplicantData(int $clientsId, array $data): void
    {
        $client = Clients::find($clientsId);

        if (!$client) {
            return;
        }

        $client->names     = trim($data['names']);
        $client->lastname1 = trim($data['lastname1']);
        $client->lastname2 = trim($data['lastname2'] ?? '') ?: null;
        $client->curp      = strtoupper(trim($data['curp']));

        if (array_key_exists('birthdate', $data)) {
            $client->birthdate = $data['birthdate'] ?: null;
        }
        if (array_key_exists('city', $data)) {
            $client->city = $data['city'] ?: null;
        }
        if (array_key_exists('country_birth_id', $data)) {
            $client->country_birth_id = ($data['country_birth_id'] ?? null) ?: null;
        }
        if (array_key_exists('state_birth_id', $data)) {
            // el formulario usa -1 como "sin estado"
            $state = $data['state_birth_id'] ?? null;
            $client->state_birth_id = ($state && (int) $state > 0) ? $state : null;
        }

        $client->save();

        if (array_key_exists('phone', $data) && trim($data['phone'] ?? '') !== '') {
            $phone = ClientsPhones::where('clients_id', $client->id)
                        ->where('type', 'Celular')
                        ->orderBy('id')
                        ->first() ?? new ClientsPhones();

            $phone->clients_id = $client->id;
            $phone->type       = 'Celular';
            $phone->number     = trim($data['phone']);
            $phone->save();
        }
    }

    /** actualiza el tipo de trámite; la opción de renovación solo aplica a renovaciones */
    protected function updateProcessType(Process $process, array $data): void
    {
        foreach (['subtype', 'age_type', 'visa_type'] as $field) {
            if (array_key_exists($field, $data) && ($field !== 'visa_type' || $process->type === 'Visa')) {
                $process->$field = $data[$field];
            }
        }

        if (array_key_exists('option_type', $data) || array_key_exists('subtype', $data)) {
            $process->option_type = $process->subtype === 'Renovación' ? (($data['option_type'] ?? $process->option_type) ?: null) : null;
        }

        $process->save();
    }

    /** crea o actualiza el pasaporte del trámite y lo agrega al historial del cliente */
    protected function savePassport(Process $process, ?array $data): void
    {
        if (trim($data['number'] ?? '') === '') {
            return;
        }

        $passport = Passport::where('process_id', $process->id)->orderByDesc('id')->first() ?? new Passport();
        $passport->process_id      = $process->id;
        $passport->number          = strtoupper(trim($data['number']));
        $passport->expedition_date = ($data['expedition_date'] ?? null) ?: null;
        $passport->expiration_date = $data['expiration_date'];

        if (array_key_exists('country_expedition', $data)) {
            $passport->expedition_countries_id = ($data['country_expedition'] ?? null) ?: null;
        }
        if (array_key_exists('state_expedition', $data)) {
            $state = $data['state_expedition'] ?? null;
            $passport->expedition_states_id = ($state && (int) $state > 0) ? $state : null;
        }
        if (array_key_exists('city_expedition', $data)) {
            $passport->expedition_city = ($data['city_expedition'] ?? null) ?: null;
        }

        $passport->save();

        if (!ClientsPassportHistory::where('clients_id', $process->clients_id)->where('number', $passport->number)->exists()) {
            ClientsPassportHistory::create([
                'clients_id'      => $process->clients_id,
                'number'          => $passport->number,
                'expedition_date' => $passport->expedition_date,
                'expiration_date' => $passport->expiration_date
            ]);
        }
    }

    /** visa anterior (renovaciones de visa); se elimina si el trámite pasa a primera vez */
    protected function saveVisa(Process $process, ?array $data): void
    {
        if ($process->type !== 'Visa') {
            return;
        }

        if ($process->subtype !== 'Renovación') {
            Visas::where('process_id', $process->id)->delete();
            return;
        }

        if (trim($data['number'] ?? '') === '') {
            return;
        }

        $visa = Visas::where('process_id', $process->id)->orderByDesc('id')->first() ?? new Visas();
        $visa->process_id      = $process->id;
        $visa->number          = strtoupper(trim($data['number']));
        $visa->expedition_date = ($data['expedition_date'] ?? null) ?: null;
        $visa->expiration_date = $data['expiration_date'];
        $visa->expedition_city = ($data['expedition_city'] ?? null) ?: null;
        $visa->save();

        if (!ClientsVisaHistory::where('clients_id', $process->clients_id)->where('number', $visa->number)->exists()) {
            ClientsVisaHistory::create([
                'clients_id'      => $process->clients_id,
                'number'          => $visa->number,
                'expedition_date' => $visa->expedition_date,
                'expiration_date' => $visa->expiration_date
            ]);
        }
    }

    /** aplica los cambios a un solicitante existente de la venta */
    protected function updateApplicant(Sales $sale, array $item, string $type): void
    {
        $process = Process::where('id', $item['process_id'] ?? null)
                        ->where('sales_id', $sale->id)
                        ->where('type', $type)
                        ->first();

        if (!$process) {
            return;
        }

        if (isset($item['process'])) {
            $this->updateProcessType($process, $item['process']);
        }

        if (isset($item['client'])) {
            $this->updateApplicantData($process->clients_id, $item['client']);
        }

        if (array_key_exists('passport', $item)) {
            $this->savePassport($process, $item['passport']);
        }

        if (array_key_exists('visa', $item) || isset($item['process'])) {
            $this->saveVisa($process, $item['visa'] ?? null);
        }
    }

    /**
     * agrega un solicitante a la venta: reutiliza el cliente si ya existe con la
     * misma CURP y crea sales_clients, process, process_status, pasaporte y visa
     *
     * @throws \RuntimeException si el cliente ya tiene un trámite de ese tipo en la venta
     */
    protected function addApplicantToSale(Sales $sale, array $item, string $type): Process
    {
        $data = $item['client'];
        $curp = strtoupper(trim($data['curp']));

        $client = Clients::where('curp', $curp)->orderByDesc('id')->first();

        if ($client && Process::where('sales_id', $sale->id)->where('clients_id', $client->id)->where('type', $type)->exists()) {
            throw new \RuntimeException('El solicitante con CURP ' . $curp . ' ya está en esta venta');
        }

        if (!$client) {
            $client = new Clients();
            $client->names     = trim($data['names']);
            $client->lastname1 = trim($data['lastname1']);
            $client->curp      = $curp;
            $client->save();
        }

        $this->updateApplicantData($client->id, $data);

        if (!SalesClients::where('sales_id', $sale->id)->where('clients_id', $client->id)->exists()) {
            SalesClients::create(['sales_id' => $sale->id, 'clients_id' => $client->id]);
        }

        $process             = new Process();
        $process->sales_id   = $sale->id;
        $process->clients_id = $client->id;
        $process->type       = $type;
        $this->updateProcessType($process, $item['process']);

        ProcessStatus::create(['process_id' => $process->id, 'status' => 'Inicio', 'is_last' => true]);

        $this->savePassport($process, $item['passport'] ?? null);
        $this->saveVisa($process, $item['visa'] ?? null);

        return $process;
    }

    /** contacto y datos de quien realiza el trámite */
    protected function updateSaleGeneral(Sales $sale, array $data, ?string $fallbackEmail = null): void
    {
        if (!empty($data['type']) && $sale->type !== $data['type']) {
            $sale->type = $data['type'];
            $sale->save();
        }

        if (!empty($data['billing'])) {
            $billing      = $data['billing'];
            $salesBilling = SalesBilling::where('sales_id', $sale->id)->first();

            if ($salesBilling) {
                $email = trim($billing['email'] ?? '');

                $salesBilling->names     = $billing['names'];
                $salesBilling->lastname1 = $billing['lastname1'];
                $salesBilling->lastname2 = $billing['lastname2'] ?? null;
                $salesBilling->email     = $email !== '' ? $email : ($fallbackEmail ?? $salesBilling->email);
                $salesBilling->phone     = $billing['phone'];
                $salesBilling->save();
            }
        }

        if (!empty($data['contact'])) {
            SalesProcess::where('sales_id', $sale->id)->update(['contact' => $data['contact']]);
        }
    }

    /**
     * precio y abono: actualiza el total y el detalle, registra el abono y
     * recalcula el saldo (total - pagado)
     */
    protected function applyPricingAndPayment(Sales $sale, string $type, ?array $pricing, ?array $payment): void
    {
        if (is_null($pricing) && is_null($payment)) {
            return;
        }

        if (!is_null($pricing)) {
            $sale->total = round((float) $pricing['total'], 2);
            $sale->save();

            $conceptId = ($pricing['sales_concepts_id'] ?? null)
                            ?: SalesDetails::where('sales_id', $sale->id)->orderBy('id')->value('sales_concepts_id');

            if ($conceptId) {
                SalesDetails::where('sales_id', $sale->id)->delete();

                $detail                    = new SalesDetails();
                $detail->sales_id          = $sale->id;
                $detail->sales_concepts_id = $conceptId;
                $detail->amount            = $sale->total;
                $detail->save();
            }
        }

        $salesProcess = SalesProcess::where('sales_id', $sale->id)->first();

        if (!is_null($payment)) {
            SalesPayment::create([
                'sales_id'       => $sale->id,
                'method_payment' => $payment['method_payment'],
                'amount'         => round((float) $payment['amount'], 2),
                'status'         => 'Pagado',
                'platform'       => 'Sistema',
                'receipt'        => $payment['receipt'],
                'reference'      => ($payment['reference'] ?? null) ?: null,
            ]);

            // en pasaporte el pagado es el abono registrado en sales_process
            if ($type !== 'Visa' && $salesProcess) {
                $salesProcess->advance_payment = round((float) $salesProcess->advance_payment + (float) $payment['amount'], 2);
            }
        }

        if ($salesProcess) {
            $paid = $type === 'Visa'
                        ? $this->salePaid($sale, $type)
                        : (float) $salesProcess->advance_payment;

            $salesProcess->payable = max(0, round((float) $sale->total - $paid, 2));
            $salesProcess->save();
        }
    }
}
