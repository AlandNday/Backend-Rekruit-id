<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import your User model
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the Authorization header exists and starts with 'Bearer '
        if ($request->header('Authorization')) {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));

            // Find a user with this api_token
            $user = User::where('api_token', $token)->first();

            if ($user) {
                // Authenticate the user for the current request
                Auth::login($user);
            }
        }

        return $next($request);
    }
}