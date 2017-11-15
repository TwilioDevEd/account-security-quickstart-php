<?php

namespace App\Http\Middleware;

use Closure;

class Check2FA
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
        if (session('authy')) {
            return $next($request);
        }

        return redirect('/2fa');
    }
}
