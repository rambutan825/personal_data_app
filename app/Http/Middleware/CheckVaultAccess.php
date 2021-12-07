<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckVaultAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestedVaultId = $request->route()->parameter('vault');

        $exists = DB::table('user_vault')->where('user_id', Auth::user()->id)
            ->where('vault_id', $requestedVaultId)
            ->count() > 0;

        if ($exists) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
