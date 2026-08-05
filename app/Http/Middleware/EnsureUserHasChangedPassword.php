<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasChangedPassword
{
    /**
     * Paksa siswa (atau akun manapun) yang belum mengganti password default
     * untuk ke halaman ganti password dulu sebelum mengakses fitur lain (PRD §9.2).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Request internal Livewire (mis. POST /livewire/update untuk aksi
        // updatePassword itu sendiri) harus dilewatkan — kalau tidak, redirect
        // ini justru memblokir AJAX call yang seharusnya mematikan flag ini,
        // menciptakan deadlock. Halaman GET tetap sepenuhnya dijaga di bawah.
        if ($request->hasHeader('X-Livewire')) {
            return $next($request);
        }

        if ($user?->must_change_password && ! $request->routeIs('password.force-change')) {
            return redirect()->route('password.force-change');
        }

        return $next($request);
    }
}
