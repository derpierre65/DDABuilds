<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 *
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$supportedLocales = config('app.locales');

		$requestedLocale = $request->header('accept-language');

		// if requested locale is not in supported locales then find a supported locale
		if (!in_array($requestedLocale, $supportedLocales)) {
			// set fallback locale
			$locale = config('app.fallback_locale');

			// split requested locale and try to find a supported locale
			foreach (explode(',', str_replace(';', ',', $requestedLocale)) as $value) {
				if (in_array($value, $supportedLocales)) {
					$locale = $value;
					break;
				}
			}
		}
		else {
			$locale = $requestedLocale;
		}

		App::setLocale($locale);

		return $next($request);
	}
}
