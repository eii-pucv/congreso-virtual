<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class CheckAuth extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            if($this->authenticate($request, $guards)) {
                $user = Auth::user();
                if(!$user || !$user->isActive() || !Auth::check()) {
                    throw new \Exception('The user has insufficient access permissions to the requested resource.');
                }
            }
            return $next($request);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()], 401);
        }
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return boolean
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        try {
            parent::authenticate($request, $guards);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if(!$request->expectsJson()) {
            return response()->json([
                'message' => 'Getting user with errors.'], 404);
        }
    }
}
