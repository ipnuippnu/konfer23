<?php

namespace App\Http\Middleware;

use App\Models\Delegator;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PendaftaranTutupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Delegator::find(\Sso::credential()->id) == null && !Carbon::parse(config('konfer.pendaftaran_sampai'))->isFuture())
            return response()->view('pendaftaran_tutup');

        return $next($request);
    }
}
