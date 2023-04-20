<?php

namespace Celysium\Launcher\Middlewares;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response|RedirectResponse
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $hash = Cache::store('file')->get('launcher_secret');
        if ($hash && Hash::check($hash, $token)) {
            return $next($request);
        }
        throw new AuthorizationException();
    }
}
