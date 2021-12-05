<?php

namespace App\Http\Middleware;

use App\Helpers\AuthHelper;
use Closure;
use Illuminate\Http\Request;

class VerifyAuthorized
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $auth = new AuthHelper();
        if (is_null($auth->user())) {
            return response()->json([], 401);
        }
        return $next($request);
    }
}
