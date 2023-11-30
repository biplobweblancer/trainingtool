<?php

namespace App\Http\Middleware\TrainingMonitoring;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $message = '';
        $code = 401;
        try {
            // check validation of the token
            JWTAuth::parseToken()->authenticate();
            return $next($request);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $message = 'Your Token Has Expired';
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $message = 'You Provide Invalid Token';
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            $message = 'You Should Provide Authentication Token';
        }
        return response()->json(['success' => false, 'message' => $message],$code);
    }
}