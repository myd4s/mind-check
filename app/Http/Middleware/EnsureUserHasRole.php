<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request. Hierarki akses: Admin ⊇ Guru BK ⊇ Siswa (PRD §2) —
     * middleware('role:guru_bk') juga meloloskan admin, bukan hanya guru_bk persis.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $required = UserRole::from($role);

        abort_unless(
            $request->user() && $request->user()->hasRoleAtLeast($required),
            403
        );

        return $next($request);
    }
}
