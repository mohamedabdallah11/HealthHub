<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        if (!auth()->check()) {
            return ApiResponse::sendResponse(401, 'un authenticated', []);
        }

        $userRole = auth()->user()->role;

        if (!in_array($userRole, $roles)) {
            return ApiResponse::sendResponse(403, 'unauthorized', []);
        }

        return $next($request);
    }    }
