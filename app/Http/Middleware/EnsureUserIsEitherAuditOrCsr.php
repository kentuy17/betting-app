<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEitherAuditOrCsr
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed_roles = ['Auditor', 'Cash-out Operator', 'Cash-in Operator'];
        if(in_array(session('role'), $allowed_roles) || hasAccess('Auditor')) {
            return $next($request);
        }

        return redirect('/home');
    }
}
