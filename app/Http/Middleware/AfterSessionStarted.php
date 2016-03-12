<?php
/**
 * Created by PhpStorm.
 * User: avenger-web
 * Date: 11.03.16
 * Time: 21:38
 */

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class AfterSessionStarted
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
        if ($token = $request->get("token")) {
            $request->session()->clear();
            $request->session()->setId(\Crypt::decrypt($token));
            $request->session()->start();
        }
        return ($next($request));
    }

}