<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceTwoFactorSetup
{
    /**
     * Handle an incoming request
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user() &&
            $request->user()->is_admin &&
            ! $request->user()->hasEnabledTwoFactorAuthentication()
        ) {
            return redirect()->route('two-factor.show');
        }
        return $next($request);
    }
}
