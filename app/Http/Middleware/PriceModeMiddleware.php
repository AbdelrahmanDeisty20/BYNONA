<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $mode = null): Response
    {
        $priceMode = strtolower($request->header('Price-Mode', $mode)); // retail or wholesale
        if (! in_array($priceMode, ['retail', 'wholesale'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Price-Mode header. Use "retail" or "wholesale".',
            ], 400);
        }

        app()->instance('price_mode', $priceMode);

        return $next($request);
    }
}
