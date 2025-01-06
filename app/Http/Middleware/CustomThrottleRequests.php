<?php
// app/Http/Middleware/CustomThrottleRequests.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomThrottle
{
    public function handle(Request $request, Closure $next)
    {
        // Lógica de controle de limite de requisições
        return $next($request);
    }
}
