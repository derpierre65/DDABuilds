<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class IsAuthenticated
{
	public function handle($request, \Closure $next, string $role = 'guest')
	{
		if ( $role === 'guest' && auth()->user() || $role === 'user' && !auth()->user() ) {
			throw new AccessDeniedHttpException();
		}

		return $next($request);
	}
}