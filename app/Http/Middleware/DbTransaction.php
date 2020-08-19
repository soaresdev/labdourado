<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DbTransaction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        DB::beginTransaction();
        try {
            $response = $next($request);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if($response->exception || ($response instanceof Response && $response->getStatusCode() > 399)) {
            DB::rollBack();
        } else {
            DB::commit();
        }
        return $response;
    }
}
