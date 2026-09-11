<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponse;

    /**
     * Handle an incoming request and check if authenticated user possesses allowed role(s).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->errorResponse('Unauthenticated.', null, 401);
        }

        // Compare case-insensitively
        $userRole = strtolower($user->role);
        $allowedRoles = array_map('strtolower', $roles);

        if (! in_array($userRole, $allowedRoles, true)) {
            return $this->errorResponse('Forbidden. You do not have permission to access this resource.', null, 403);
        }

        return $next($request);
    }
}
