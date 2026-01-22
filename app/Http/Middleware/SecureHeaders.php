<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options','DENY'); // يمنع clickjacking
        $response->headers->set('X-Content-Type-Options','nosniff'); // يمنع sniffing
        $response->headers->set('Referrer-Policy','no-referrer'); 
        $response->headers->set('Strict-Transport-Security','max-age=63072000; includeSubDomains; preload'); // يجبر HTTPS
        $response->headers->set('X-XSS-Protection','1; mode=block'); // حماية ضد XSS

        return $response;
    }
}