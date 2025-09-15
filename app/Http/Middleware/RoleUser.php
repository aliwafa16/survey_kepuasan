<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Kalau user belum login atau tidak punya role
        if (!$user || !in_array($user->f_role, $roles)) {
            return redirect('dashboard'); // atau abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

