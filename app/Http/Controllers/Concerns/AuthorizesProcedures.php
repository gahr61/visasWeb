<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Reglas de autorización para ver/editar trámites.
 *
 * - Empleados: necesitan el permiso correspondiente (Spatie).
 * - Usuarios con rol `cliente`: solo pueden ver y llenar la información de
 *   SUS trámites de visa EN CURSO (ventas ligadas a ellos en `sales_users`,
 *   que no estén finalizadas). Cualquier otra venta/cliente/trámite se niega.
 */
trait AuthorizesProcedures
{
    const EDIT_PERMISSIONS = ['procedure_visa_update', 'procedure_passport_update'];

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

    /** ¿El usuario puede operar sobre la venta $salesId con el permiso $permission? */
    protected function canManageSale(User $user, $salesId, string $permission): bool
    {
        if ($this->isClientUser($user)) {
            return in_array((int) $salesId, $this->clientActiveSalesIds($user), true);
        }

        return $user->can($permission);
    }

    /** ¿El usuario puede editar los datos del cliente (solicitante) $clientsId? */
    protected function canManageClient(User $user, $clientsId): bool
    {
        if ($this->isClientUser($user)) {
            $activeSales = $this->clientActiveSalesIds($user);

            return !empty($activeSales) && DB::table('sales_clients')
                ->where('clients_id', $clientsId)
                ->whereIn('sales_id', $activeSales)
                ->exists();
        }

        return $user->canAny(self::EDIT_PERMISSIONS);
    }

    /** ¿El usuario puede editar el trámite $processId? */
    protected function canManageProcess(User $user, $processId): bool
    {
        if ($this->isClientUser($user)) {
            $salesId = DB::table('process')->where('id', $processId)->value('sales_id');

            return !is_null($salesId)
                && in_array((int) $salesId, $this->clientActiveSalesIds($user), true);
        }

        return $user->canAny(self::EDIT_PERMISSIONS);
    }

    protected function forbidden(string $message = 'No tienes permiso para realizar esta acción'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 403);
    }
}
