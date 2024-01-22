<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrimTelephone
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
        $request_all = $request->all();
        $disable_trim_tel = boolval($request->disable_trim_tel);
        if (!$disable_trim_tel) {
            foreach ($request_all as $key => $value) {
                if (Str::contains($key, ['phone', 'tel'])) {
                    $request->merge([$key => str_replace('-', '', $request->{$key})]);
                }
            }
        }
        return $next($request);
    }
}