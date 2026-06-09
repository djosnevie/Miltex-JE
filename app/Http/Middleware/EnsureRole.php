<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: ->middleware('role:admin,super_admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        if (! empty($roles) && ! $user->hasRole($roles)) {
            abort(403, 'Accès refusé : vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
