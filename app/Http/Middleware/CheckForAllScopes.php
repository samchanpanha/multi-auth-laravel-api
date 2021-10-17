<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class CheckForAllScopes
{
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        if(! $request->user() || ! $request->user()->token()){
            throw new AuthenticationException;
        }
        foreach ($scopes as $key => $scope) {
            if($request->user()->tokenCan($scope)){
                return $next($request);
            }
        }

        return response([
            "message" => "Not Authorized"
        ], 403);
    }
}
