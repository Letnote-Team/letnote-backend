<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConvertRequestFieldsToCamelCase
{
    /**
     * Convert camelCase params into snake_case.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $replaced = [];
        foreach ($request->all() as $key => $value) {
            $replaced[Str::snake($key)] = $value;
        }
        $request->replace($replaced);

        return $next($request);
    }
}
