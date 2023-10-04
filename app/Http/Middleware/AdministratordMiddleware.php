<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministratordMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      if (!auth()->check()) {
        return redirect('/app/login');
      }
        if (auth()->user() && auth()->user()->isAdmin()) {
          return $next($request);
        }

        return redirect('/app');
    }
}
