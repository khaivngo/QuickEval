<?php

namespace App\Http\Middleware;

use Closure;

use App\Experiment as Experiment;

class ReturnExperiment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( auth()->user() && (auth()->user()->role >= 2) ) {
            return $next($request);
        }

        return response('Unauthorized');
    }
}
