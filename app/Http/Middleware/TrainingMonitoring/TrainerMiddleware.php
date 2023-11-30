<?php

namespace App\Http\Middleware\TrainingMonitoring;

use Closure;
use Illuminate\Http\Request;

class TrainerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Auth Token Expired']);
        }
        if (auth()->user()->role_id != 9) {
            return response()->json(['success' => false, 'message' => 'Authorization Failed']);
        }
        return $next($request);
    }
}
