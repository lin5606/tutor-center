<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict a route to authenticated users who have the requested RBAC slug.
 * Register the `permission` alias in bootstrap/app.php.
 */
class CheckPermission
{
    /** TODO: Read the permission slug from the route middleware and authorize the request. */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        abort_unless($request->user() && $request->user()->hasPermission($permission), 403);

        return $next($request);
    }
}
