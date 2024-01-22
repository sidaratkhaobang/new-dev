<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $method = $request->route()->getActionMethod();
        $is_view = (strcmp($method, 'show') == 0) ? true : false;
        set_view($is_view);
        return $next($request);
    }
}
