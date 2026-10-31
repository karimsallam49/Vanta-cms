<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate a route by RBAC permission.
 *
 *   'perm:motorcycles'      — action resolved from the route name suffix:
 *                             index→view, create/store→create, edit/update→edit,
 *                             destroy→delete, *.import.*→import, *.export→export
 *   'perm:reports.export'   — explicit permission, used as-is
 */
class RequirePermission
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $permission = str_contains($section, '.')
            ? $section
            : $section.'.'.$this->actionFor($request);

        abort_unless($user->hasPermission($permission), 403);

        return $next($request);
    }

    private function actionFor(Request $request): string
    {
        $name = $request->route()?->getName() ?? '';

        return match (true) {
            str_contains($name, '.import.') => 'import',
            str_ends_with($name, '.export') => 'export',
            str_ends_with($name, '.create'), str_ends_with($name, '.store') => 'create',
            str_ends_with($name, '.edit'), str_ends_with($name, '.update') => 'edit',
            str_ends_with($name, '.destroy') => 'delete',
            default => 'view',
        };
    }
}
