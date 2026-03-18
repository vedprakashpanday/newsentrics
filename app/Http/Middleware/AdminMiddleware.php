<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 1. Auth Facade import kiya
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 2. Editor ko batane ke liye (Type Hinting) ki $user asal mein hamara User model hai
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // 3. Clean aur error-free condition
        if ($user && $user->is_admin) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Aapko is page ka access nahi hai.');
    }
}