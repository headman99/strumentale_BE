<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class AllowCrawler
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
       $allowedHosts = ["localhost","93.93.117.235"];
        
        $requestHost = $request->ip();
        $verify = in_array($requestHost,$allowedHosts);
        if(!$verify){
            $requestInfo = [
                'host' => $request->host(),
                'ip' => $request->getClientIp(),
                'url' => $request->getRequestUri(),
                'agent' => $request->header('User-Agent'),
            ];
            throw new SuspiciousOperationException('This host is not allowed');
            //return response($requestInfo,500);
           
        }
            
        return $next($request);
    }
}
