<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next):JsonResponse
    {
        $user=Auth::user();
        if(!$user||$user->role!='admin'){
            return response()->json([
                'message'=>'دسترسی غیرمجاز'
            ],403);
        }
        return $next($request);
    }
}
