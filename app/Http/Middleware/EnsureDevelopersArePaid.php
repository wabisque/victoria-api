<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class EnsureDevelopersArePaid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $date): Response
    {
        $date = Carbon::parse($date);
        $now = now();

        if($date->isBefore($now))
        {
            throw new ServiceUnavailableHttpException(message: 'Please pay the developers');
        }

        return $next($request);
    }
}
