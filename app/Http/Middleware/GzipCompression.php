<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipCompression
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if Gzip is supported by browser and not already compressed
        if (str_contains($request->header('Accept-Encoding'), 'gzip') && !app()->isLocal()) {
            $buffer = $response->getContent();
            if (strlen($buffer) > 0) {
                $response->setContent(gzencode($buffer, 9));
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }

        return $response;
    }
}
