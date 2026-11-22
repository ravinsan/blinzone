<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;

class AuthenticateApi
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('sanctum');

        if (!$user) {

            Log::warning('Unauthorized API Request', [
                'url'    => $request->fullUrl(),
                'method' => $request->method(),
                'ip'     => $request->ip(),
            ]);

            return $this->errorResponse(
                'Unauthorized. Please login first.',
                401
            );
        }

        // Authenticated user ko request me set karna
        auth()->setUser($user);

        return $next($request);
    }
}