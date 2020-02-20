<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if($user && $user->isActive() && Auth::check()) {
            foreach($roles as $role) {
                if($user->hasRole($role)) {
                    return $next($request);
                }
            }
        }
        return response()->json([
            'message' => 'The user has insufficient access permissions to the requested resource.'], 401);
    }
}
