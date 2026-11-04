<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Un usuario con rol `cliente` solo puede usar los endpoints necesarios para
 * consultar y llenar la información de su propio trámite de visa en curso.
 * Cualquier otro endpoint protegido responde 403.
 *
 * La pertenencia (que la venta/cliente/trámite sea suyo y esté en curso) se
 * valida en cada controlador con App\Http\Controllers\Concerns\AuthorizesProcedures.
 */
class RestrictClientRole
{
    /** [método HTTP, URI de la ruta sin el prefijo api/v1/] */
    const ALLOWED = [
        // sesión
        ['GET',  'auth/logout'],
        ['POST', 'users/reset'],

        // catálogos que usa el formulario
        ['GET',  'countries/list'],
        ['GET',  'countries/states/{id}'],
        ['GET',  'occupations/list'],
        ['GET',  'relationship_types/list'],

        // su trámite
        ['GET',  'sales/visa/client/list'],
        ['GET',  'procedures/visas/{id}/details'],

        // llenado de información
        ['PUT',  'clients/update/{id}'],
        ['PUT',  'clients/address/update'],
        ['PUT',  'clients/phones/update'],
        ['POST', 'clients/parents'],
        ['POST', 'clients/occupations'],
        ['POST', 'clients/studies'],
        ['POST', 'clients/languages'],
        ['POST', 'clients/travel'],
        ['POST', 'clients/social_networks'],
        ['PUT',  'passport/{id}/update'],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'cliente') {
            return $next($request);
        }

        $uri    = preg_replace('#^api/v1/#', '', $request->route()->uri());
        $method = $request->method();

        foreach (self::ALLOWED as [$allowedMethod, $allowedUri]) {
            if ($allowedMethod === $method && $allowedUri === $uri) {
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No tienes permiso para realizar esta acción'
        ], 403);
    }
}
