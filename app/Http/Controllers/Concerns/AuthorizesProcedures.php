<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Reglas de autorización para ver/editar trámites.
 *
 * - Empleados: necesitan el permiso correspondiente (Spatie). El rol `ventas`
 *   además solo opera sobre las ventas que tiene asignadas en `sales_users`.
 * - Usuarios con rol `cliente`: solo pueden ver y llenar la información de
 *   SUS trámites de visa EN CURSO (ventas ligadas a ellos en `sales_users`,
 *   que no estén finalizadas). Cualquier otra venta/cliente/trámite se niega.
 */
trait AuthorizesProcedures
{
    /** Valores válidos de los enums de `process` */
    const PROCESS_SUBTYPES     = ['Primera vez', 'Renovación'];
    const PROCESS_AGE_TYPES    = ['Mayor de edad', 'Menor de edad'];
    const PROCESS_VISA_TYPES   = ['Turista', 'Trabajo'];
    const PROCESS_OPTION_TYPES = ['Vencida o por vencer', 'Extraviado', 'Deteriorado', 'Cambio de datos', 'Vencido +4 años'];

    /**
     * Valida el tipo de trámite enviado (subtype/age_type/option_type/visa_type).
     * Regresa el mensaje de error o null si es válido.
     */
    protected function invalidProcessType(array $data, bool $isVisa): ?string
    {
        if (array_key_exists('subtype', $data) && !in_array($data['subtype'], self::PROCESS_SUBTYPES, true)) {
            return 'Tipo de trámite no válido';
        }
        if (array_key_exists('age_type', $data) && !in_array($data['age_type'], self::PROCESS_AGE_TYPES, true)) {
            return 'Tipo de edad no válido';
        }
        if (!empty($data['option_type']) && !in_array($data['option_type'], self::PROCESS_OPTION_TYPES, true)) {
            return 'Opción de renovación no válida';
        }
        if ($isVisa && array_key_exists('visa_type', $data) && !in_array($data['visa_type'], self::PROCESS_VISA_TYPES, true)) {
            return 'Tipo de visa no válido';
        }

        return null;
    }

    /** Estatus de venta (sales_status) que cierran el trámite para el cliente */
    const CLOSED_SALE_STATUSES = ['Finalizado'];

    /** Estatus de trámite (process_status) que cierran el trámite para el cliente */
    const CLOSED_PROCESS_STATUSES = ['Entregado', 'Finalizado'];

    /** Caché por request de las ventas en curso del cliente */
    private ?array $clientActiveSalesIds = null;

    protected function isClientUser(User $user): bool
    {
        return $user->role === 'cliente';
    }

    /**
     * Una venta está en curso si su último estatus no la cierra y tiene al
     * menos un trámite de visa cuyo último estatus tampoco esté cerrado.
     */
    protected function isSaleInProgress($salesId): bool
    {
        $saleStatus = DB::table('sales_status')
            ->where('sales_id', $salesId)
            ->orderByDesc('id')
            ->value('status');

        if (in_array($saleStatus, self::CLOSED_SALE_STATUSES, true)) {
            return false;
        }

        $processIds = DB::table('process')
            ->where('sales_id', $salesId)
            ->where('type', 'Visa')
            ->pluck('id');

        foreach ($processIds as $processId) {
            $processStatus = DB::table('process_status')
                ->where('process_id', $processId)
                ->orderByDesc('id')
                ->value('status');

            if (!in_array($processStatus, self::CLOSED_PROCESS_STATUSES, true)) {
                return true;
            }
        }

        return false;
    }

    /** IDs de las ventas del usuario cliente que siguen en curso */
    protected function clientActiveSalesIds(User $user): array
    {
        if (!is_null($this->clientActiveSalesIds)) {
            return $this->clientActiveSalesIds;
        }

        $salesIds = DB::table('sales_users')
            ->where('users_id', $user->id)
            ->pluck('sales_id')
            ->unique();

        return $this->clientActiveSalesIds = $salesIds
            ->filter(fn ($salesId) => $this->isSaleInProgress($salesId))
            ->map(fn ($salesId) => (int) $salesId)
            ->values()
            ->all();
    }

    /** El rol `ventas` solo opera sobre las ventas que tiene asignadas */
    protected function isSellerUser(User $user): bool
    {
        return $user->role === 'ventas';
    }

    /** ¿La venta está asignada al usuario en sales_users? */
    protected function isAssignedToSale(User $user, $salesId): bool
    {
        return DB::table('sales_users')
            ->where('sales_id', $salesId)
            ->where('users_id', $user->id)
            ->exists();
    }

    /** ¿El usuario puede operar sobre la venta $salesId con el permiso $permission? */
    protected function canManageSale(User $user, $salesId, string $permission): bool
    {
        if ($this->isClientUser($user)) {
            return in_array((int) $salesId, $this->clientActiveSalesIds($user), true);
        }

        if (!$user->can($permission)) {
            return false;
        }

        return !$this->isSellerUser($user) || $this->isAssignedToSale($user, $salesId);
    }

    /** ¿El usuario puede editar los datos del cliente (solicitante) $clientsId? */
    protected function canManageClient(User $user, $clientsId, string $permission = 'procedure_visa_update'): bool
    {
        if ($this->isClientUser($user)) {
            $activeSales = $this->clientActiveSalesIds($user);

            return !empty($activeSales) && DB::table('sales_clients')
                ->where('clients_id', $clientsId)
                ->whereIn('sales_id', $activeSales)
                ->exists();
        }

        if (!$user->can($permission)) {
            return false;
        }

        // ventas: el solicitante debe pertenecer a alguna de sus ventas
        return !$this->isSellerUser($user) || DB::table('sales_clients')
            ->join('sales_users', 'sales_users.sales_id', '=', 'sales_clients.sales_id')
            ->where('sales_clients.clients_id', $clientsId)
            ->where('sales_users.users_id', $user->id)
            ->exists();
    }

    /** ¿El usuario puede editar el trámite $processId? */
    protected function canManageProcess(User $user, $processId, string $permission = 'procedure_visa_update'): bool
    {
        $salesId = DB::table('process')->where('id', $processId)->value('sales_id');

        if (is_null($salesId)) {
            return false;
        }

        return $this->canManageSale($user, $salesId, $permission);
    }

    protected function forbidden(string $message = 'No tienes permiso para realizar esta acción'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 403);
    }
}
