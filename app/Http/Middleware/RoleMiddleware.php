<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $allowed = [];
        foreach ($roles as $roleArg) {
            foreach (explode(',', $roleArg) as $r) {
                $r = trim($r);
                if ($r !== '') $allowed[] = $r;
            }
        }

        if (!in_array($user->role, $allowed, true)) {
            abort(403, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}
